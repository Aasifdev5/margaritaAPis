<?php

namespace App\Http\Controllers;


use App\Mail\SendMailreset;

use App\Models\BankDetails;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;

use App\Models\Category;
use App\Models\City;
use App\Models\Comment;

use App\Models\Country;

use App\Models\News;
use App\Models\Notification;

use App\Models\Page;
use App\Models\PasswordReset;
use App\Models\Reaction;
use App\Models\Sales;

use App\Models\SupportTicketQuestion;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\VerifyEmailNotification;
use App\Traits\SendNotification;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;



function getIp()
{
    $ip = null;
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else {
        if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
    }
    return $ip;
}

class UserController extends Controller
{
    use SendNotification;



    public function home()
    {

        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $sliders = Banner::all()->map(function ($slider) {
            // Split every 4 words into a new line
            $words = explode(' ', $slider->title1);
            $chunks = array_chunk($words, 4); // Adjust 4 for the desired number of words per line
            $slider->title1 = implode('<br>', array_map(fn($chunk) => implode(' ', $chunk), $chunks));
            return $slider;
        });

        return view('index', compact('user_session', 'sliders'));
    }

    public function membership()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        return view('membership', compact('user_session'));
    }
    public function segundaFase()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        return view('segundaFase', compact('user_session'));
    }
    public function land()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        return view('land', compact('user_session'));
    }
    public function vacancy()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        return view('vacancy', compact('user_session'));
    }
    public function about()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $pages = Page::all();
        return view('about', compact('user_session', 'pages'));
    }
    public function faq()
    {


        $user_session    = User::where('id', Session::get('LoggedIn'))->first();

        $pages = Page::all();
        return view('faq', compact('user_session', 'pages'));
    }
    public function ownership()
    {


        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $pages = Page::all();
        return view('ownership', compact('user_session', 'pages'));
    }
    public function Userlogin()
    {
        $pages = Page::all();
        return view('login', compact('pages'));
    }
    public function admin()
    {
        return view('admin.admin');
    }
    public function signup(Request $request)
    {
        // Get all keys from the request
        $keys = array_keys($request->all());

        // Check if the keys array is not empty before accessing the first key
        $refer = !empty($keys) ? $keys[0] : null;

        // Fetch the necessary data
        $pages = Page::all();
        $countries = Country::all();
        $cities = City::all();

        // Pass the $refer value to the view
        return view('register', compact('pages', 'countries', 'cities', 'refer'));
    }


    public function registration(Request $request)
    {
        // Validate input fields
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'code' => 'required',
            'password' => ['required', 'string', 'min:8', 'max:30'],
            'mobile_number' => 'required|string|max:15',

        ]);

        // Handle mobile number with prefix
        $prefixedMobileNumber = "591" . $request->mobile_number;

        // Create a new user
        $user = User::create([
            'account_type' => 'user',
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'custom_password' => $request->password, // Storing plain password is insecure; reconsider this
            'mobile_number' => $prefixedMobileNumber,
            'id_number' => $request->code,
            'ip_address' => getIp(), // Assuming getIpAddress is a defined method

        ]);

        if ($user) {
            // Send notification for registration
            $text = 'A new member has registered on the platform.';
            $target_url = route('users');
            $this->sendForApi($text, 1, $target_url, $user->id, $user->id);

            // Store user ID in session for further steps
            session(['LoggedIn' => $user->id]);

            return redirect('dashboard')->with([

                'user' => $user
            ]);
        }

        return back()->with('fail', 'User registration failed.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                // dd($user);
                // if ($user->email_verified_at === null) {
                //     return back()->with('fail', 'Your account is not verified. Please verify your email.');
                // }

                $user->update(['is_online' => 1, 'last_seen' => Carbon::now('UTC')]);
                $request->session()->put('LoggedIn', $user->id);
                $user_session = User::where('id', Session::get('LoggedIn'))->first();
                $request->session()->put('user_session', $user);
                $userId = Session::get('LoggedIn');

                return redirect('news');
            } else {
                return back()->with('fail', 'Password does not match');
            }
        } else {
            return back()->with('fail', 'Email is not registered');
        }
    }


    function userNotifications()
    {
        $notifications = Notification::where('user_type', 2)
            ->where('is_seen', 'no')
            ->orderByDesc('created_at')
            ->paginate(5);
        return response()->json($notifications);
    }

    public function term()
    {

        $pages = Page::all();
        $user_session = User::where('id', Session::get('LoggedIn'))->first();



        // Pass the order ID to the success view for triggering the PDF download
        return view('term', compact('user_session',  'pages'));
    }





    public function verification()
    {
        if (Session::has('LoggedIn')) {

            $pages = Page::all();
            $user_session = User::where('id', Session::get('LoggedIn'))->first();


            return view('verification', compact('user_session',   'pages'));
        } else {
            return Redirect()->with('fail', 'Tienes que iniciar sesión primero');
        }
    }
    public function blog_detail(Request $request)
    {

        $blog_detail = Blog::where('slug', $request->slug)->first();
        // dd($blog_detail);
        $data['blogComments'] = BlogComment::active();
        $blogComments = $data['blogComments']->whereNull('parent_id')->get();
        // Fetch blog with count of active comments
        $commentCount = BlogComment::where('blog_id', $blog_detail->id)

            ->where('status', '1') // If you have a status for active comments
            ->count();

        $pages = Page::all();
        $latest_posts = Blog::orderBy('id', 'DESC')->paginate(3);
        $user_session = User::where('id', Session::get('LoggedIn'))->first();
        // dd($request->id);
        return view('blog_detail', compact('blogComments', 'user_session', 'blog_detail', 'pages', 'latest_posts', 'commentCount'));
    }




    public function addpaymentmethod(Request $request)
    {
        // Retrieve the user ID from the session
        $userId = session('LoggedIn');

        if (!$userId) {
            return back()->with('fail', 'Session expired. Please log in again.');
        }

        // Fetch the user and associated data
        $user_session = User::find($userId);
        $qrcode = BankDetails::orderby('id', 'desc')->first();
        $pages = Page::all();

        return view('addpaymentmethod', compact('user_session', 'pages', 'qrcode'));
    }

    public function ayuda(Request $request)
    {
        $query = $request->get('query'); // Capture the search query

        $supportQuestions = SupportTicketQuestion::when($query, function ($queryBuilder) use ($query) {
            $queryBuilder->where('question', 'like', '%' . $query . '%')
                ->orWhere('answer', 'like', '%' . $query . '%');
        })->paginate(9); // Adjust number of items per page


        $user_session = User::where('id', Session::get('LoggedIn'))->first();
        $pages = Page::all();

        return view('ayuda', compact('user_session', 'pages', 'supportQuestions', 'query'));
    }


    public function sendResetPasswordLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('fail', 'Email address not found.');
        }
        $token = Str::random(40);


        $datetime = Carbon::now()->format('Y-m-d H:i:s');

        $token = PasswordReset::updateOrCreate(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => $datetime
            ]
        );

        // Send the password reset notification
        $user->notify(new ResetPasswordNotification($token));

        return back()->with('success', 'Enlace para restablecer la contraseña enviado correctamente.');
    }


    public function dashboard()
    {
        if (Session::has('LoggedIn')) {
            $user_session = User::where('id', Session::get('LoggedIn'))->first();

            // dd($sales);
            $pages = Page::all();

            return view('dashboard', compact('user_session', 'pages'));
        } else {
            // Redirect to the login page if the user is not logged in
            return redirect()->route('Userlogin'); // or use 'login' if you have a named route for login
        }
    }
    public function welcome()
    {
        if (Session::has('LoggedIn')) {
            $user_session = User::where('id', Session::get('LoggedIn'))->first();
            $pages = Page::all();

            return view('welcome', compact('user_session', 'pages'));
        } else {
            // Redirect to the login page if the user is not logged in
            return redirect()->route('Userlogin'); // or use 'login' if you have a named route for login
        }
    }

    public function blogs(Request $request)
    {
        $query = $request->get('query'); // Get the search query

        // Fetch blogs, filtering by the search query if it exists
        $blogs = Blog::when($query, function ($queryBuilder) use ($query) {
            $queryBuilder->where('title', 'like', '%' . $query . '%')
                ->orWhere('short_description', 'like', '%' . $query . '%');
        })->orderBy('id', 'DESC')->paginate(9);

        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $data['blogComments'] = BlogComment::active();
        $blogComments = $data['blogComments']->whereNull('parent_id')->get();
        $pages = Page::all();
        $latest_posts = Blog::orderBy('id', 'DESC')->paginate(3);

        return view('blog', compact('user_session', 'latest_posts', 'blogs', 'pages', 'blogComments', 'query'));
    }
    public function newsDetails(Request $request)
    {
        if (Session::has('LoggedIn')) {
            $news = News::findOrFail($request->id);
            $comments = Comment::where('news_id', $request->id)->latest()->get();
            $reactions = Reaction::where('news_id', $request->id)->pluck('count', 'type');
            $latest_posts = News::latest()->take(3)->get();
            $user_session = User::find(Session::get('LoggedIn'));

            return view('newsDetails', compact('user_session', 'news', 'latest_posts', 'comments', 'reactions'));
        } else {
            return redirect('Userlogin')->with('fail', 'Tienes que iniciar sesión primero');
        }
    }

    public function Reactionstore(Request $request)
    {
        $request->validate([
            'news_id' => 'required|exists:news,id',
            'type' => 'required|in:cool,bad,lol,sad'
        ]);

        $reaction = Reaction::firstOrCreate([
            'news_id' => $request->news_id,
            'type' => $request->type,
        ]);

        $reaction->increment('count');

        return response()->json(['success' => true, 'count' => $reaction->count]);
    }

    public function Commentstore(Request $request)
{
    // // Validate the request
    // $request->validate([
    //     'news_id' => 'required|exists:news,id',
    //     'author' => 'required|string|max:255',
    //     'email' => 'required|email',
    //     'comment' => 'required|string',
    //     'privacy_policy' => 'accepted' // Ensure this field is validated
    // ]);
// Get the logged-in user's ID from the session
$user_id = Session::get('LoggedIn');
    // Create the comment
    $comment = Comment::create([
        'news_id' => $request->news_id,
        'user_id' => $user_id,
        'author' => $request->author,
        'email' => $request->email,
        'comment' => $request->comment
    ]);

    // Return the created comment as JSON
    return response()->json([
        'message' => 'Comentario enviado con éxito.',
        'comment' => $comment
    ]);
}
    public function news(Request $request)
    {
        if (Session::has('LoggedIn')) {
            $query = $request->get('query');
            $user_session = User::where('id', Session::get('LoggedIn'))->first();

            $latest_posts = News::when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where('title', 'like', '%' . $query . '%')
                    ->orWhere('content', 'like', '%' . $query . '%')->orWhere('author', 'like', '%' . $query . '%');
            })->orderBy('id', 'DESC')->paginate(9);

            return view('news', compact('user_session', 'latest_posts'));
        } else {
            return Redirect('Userlogin')->with('fail', 'Tienes que iniciar sesión primero');
        }
    }
    public function news_category($slug)
    {
        $news = DB::table('blogs')
            ->join('blog_categories', 'blogs.blog_category_id', '=', 'blog_categories.id')
            ->where('blog_categories.slug', $slug)
            ->select('blogs.*')
            ->get();

        // dd($news);
        $user_session = User::where('id', Session::get('LoggedIn'))->first();
        $title = $slug;
        $data['blogComments'] = BlogComment::active();
        $blogComments = $data['blogComments']->whereNull('parent_id')->get();
        $pages = Page::all();
        $latest_posts = Blog::orderBy('id', 'DESC')->paginate(3);

        return view('news_category', compact('user_session', 'latest_posts', 'title', 'news', 'pages', 'blogComments'));
    }
    public function blogCommentStore(Request $request)
    {
        $comment = new BlogComment();
        $comment->blog_id = $request->blog_id;
        $comment->user_id = $request->user_id;
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->comment = $request->comment;
        $comment->status = 1;

        if ($comment->save()) {
            // Retrieve updated comments for the specific blog
            $blogComments = BlogComment::active()
                ->where('blog_id', $request->blog_id)
                ->whereNull('parent_id')
                ->get();

            return response()->json([
                'success' => true,

            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    public function blogCommentReplyStore(Request $request)
    {
        // dd($request->all());
        if ($request->user_id && $request->reply_comment) {
            $comment = new BlogComment();
            $comment->blog_id = $request->blog_id;
            $comment->user_id = $request->user_id;

            $comment->comment = $request->reply_comment;
            $comment->status = 1;
            $comment->parent_id = $request->parent_id;
            $comment->save();

            return response()->json([
                'success' => true,
                'message' => 'Comment successfully added.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store comment.',
            ]);
        }
    }

    public function searchBlogList(Request $request)
    {
        $data['blogs'] = Blog::active()->where('title', 'like', "%{$request->title}%")->get();


        return view('frontend.blog.render-search-blog-list', $data);
    }


    public function service()
    {

        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $pages = Page::all();


        return view('service', compact('user_session', 'pages'));
    }
    public function contact()
    {

        $user_session = User::where('id', Session::get('LoggedIn'))->first();

        $pages = Page::all();


        return view('contact', compact('user_session', 'pages'));
    }


    public function book(Request $request)
    {
        if (Session::has('LoggedIn')) {
            // Get the currently logged-in user
            $user_session = User::where('id', Session::get('LoggedIn'))->first();

            // Fetch users with their children (for multi-level marketing)
            $users = User::with('children')->where('refer', $user_session->id)->get();  // Get users referred by the logged-in user

            return view('book', compact('user_session', 'users'));
        } else {
            return Redirect('Userlogin')->with('fail', 'Tienes que iniciar sesión primero');
        }
    }













    public function change_password(Request $request)
    {

        $data = array();
        if (Session::has('LoggedIn')) {
            $user_session = User::where('id', '=', Session::get('LoggedIn'))->first();
        }
        $pages = Page::all();
        return view('change_password', compact('user_session', 'pages'));
    }
    public function update_password(Request $request)
    {


        $request->validate([
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        # Update the new Password
        $data = User::find($request->user_id);
        $data->password = ($request->new_password);
        $data->save();

        return back()->with('success', 'Successfully Updated');
    }



    public function logout(Request $request)
    {
        if (Session::has('LoggedIn')) {
            $data = User::find(Session::get('LoggedIn'));
            if ($data) {
                $data->update(['is_online' => 0, 'last_seen' => Carbon::now('America/La_Paz')]);
            }

            Session::forget('LoggedIn');
            Session::forget('user_session');
            $request->session()->invalidate();
            return redirect('/');
        }

        return redirect('/'); // In case session is not set
    }


    public function edit_profile()
    {
        if (Session::has('LoggedIn')) {
            $user_session = User::where('id', Session::get('LoggedIn'))->first();
            $pages = Page::all();
            return view('edit_profile', compact('user_session', 'pages'));
        }
    }
    public function update_profile(Request $request)
    {
        // dd($request->all());
        try {
            $user = User::find($request->user_id);

            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo');
                $imageName = time() . '_' . $profilePhoto->getClientOriginalName();
                $profilePhoto->move(public_path('profile_photo'), $imageName);

                // Elimina la foto anterior si existe y guarda la nueva
                if ($user->profile_photo && file_exists(public_path('profile_photo/' . $user->profile_photo))) {
                    unlink(public_path('profile_photo/' . $user->profile_photo));
                }
                $user->profile_photo = $imageName;
            }

            $user->name = $request->name;
            $user->about = $request->bio;
            $user->username = $request->username;
            $user->mobile_number = "591" . $request->mobile_number;
            $user->email = $request->email;
            $user->facebook = $request->facebook ?? $user->facebook;
            $user->instagram = $request->instagram ?? $user->instagram;
            $user->linkedin = $request->linkedin ?? $user->linkedin;
            $user->twitter = $request->twitter ?? $user->twitter;

            if ($user->save()) {
                return redirect()->back()->with('success', 'Perfil actualizado con éxito');
            } else {
                return redirect()->back()->with('fail', 'Error al actualizar el perfil');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('fail', 'Error: ' . $e->getMessage());
        }
    }





    public function forget_password()
    {
        $pages = Page::all();
        return view('forget_password', compact('pages'));
    }
    public function forget_mail(Request $request)
    {
        try {
            $customer = User::where('email', $request->email)->get();

            if (count($customer) > 0) {

                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/ResetPasswordLoad?token=' . $token;

                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Password Reset";
                $data['body'] = "Please click on below link to reset your password.";
                $data['auth'] = "SkyForecastingTeam";

                Mail::to($request->email)->send(
                    new SendMailreset(
                        $token,
                        $request->email,
                        $data
                    )
                );


                $datetime = Carbon::now()->format('Y-m-d H:i:s');

                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                );
                return redirect('forget_password')->with('success', 'Please check your mail to reset your password');
                // return response()->json(['success' => true, 'msg' => 'Please check your mail to reset your password.']);
            } else {
                return redirect('forget_password')->with('fail', 'User not found');
                // return response()->json(['fail' => false, 'msg' => 'User not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }
    public function ResetPasswordLoad(Request $request)
    {

        $resetData =  PasswordReset::where('token', $request->token)->get();
        if (isset($request->token) && count($resetData) > 0) {
            $customer = User::where('email', $resetData[0]['email'])->get();
            $pages = Page::all();
            return view('ResetPasswordLoad', ['customer' => $customer], compact('pages'));
        }
    }



    public function ResetPassword(Request $request)
    {
        // Validate the input
        $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'max:30'],
            'confirm_password' => ['required', 'same:new_password'],
        ]);

        // Retrieve the user by email
        $data = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$data) {
            return redirect()->back()->with('fail', 'User not found.');
        }

        // Hash and save the new password
        $data->password = bcrypt($request->new_password);
        $data->custom_password = $request->new_password; // If you need plain text storage
        $data->update();

        // Delete the password reset entry
        PasswordReset::where('email', $data->email)->delete();
        if ($data->is_super_admin == 1) {
            // Redirigir a la página de inicio de sesión del administrador
            return redirect()->to('admin/login'); // O usar 'http://127.0.0.1:8000/admin/login' si es necesario
        } else {
            // Redirigir a la página de inicio de sesión del usuario con un mensaje de éxito
            return redirect('Userlogin')->with('success', 'Contraseña restablecida con éxito.');
        }
    }
}
