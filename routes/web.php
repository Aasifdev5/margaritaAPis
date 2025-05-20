<?php

use App\Http\Controllers\Admin\AboutUsController;

use App\Http\Controllers\Admin\Admin;


use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ContactUsController;


use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\EmailAppController;
use App\Http\Controllers\Admin\EventsController;
use App\Http\Controllers\Admin\FacebookSocialiteController;

use App\Http\Controllers\Admin\ForumCategoryController;

use App\Http\Controllers\Admin\ForumController;

use App\Http\Controllers\Admin\GoogleController;


use App\Http\Controllers\Admin\HomeSettingController;

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocationController;



use App\Http\Controllers\Admin\MailTemplateController;

use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;

use App\Http\Controllers\Admin\Pages;

use App\Http\Controllers\Admin\PortfolioController;

use App\Http\Controllers\Admin\ProductController;

use App\Http\Controllers\Admin\ResetPasswordController;


use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\QRCodeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\SetLocale;
use App\Models\Language;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;










/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Catch-All Route for Non-Existent Pages
Route::fallback(function () {
    return response()->view('others.error_pages.error_page5', [], 404);
});

//admin unique layouts

Route::view('dashboard-light', 'admin_unique_layout.light_dashboard')->name('dashboard-light');
Route::view('e-commerce-light', 'admin_unique_layout.e-commerce-light')->name('e-commerce-light');
Route::view('general-widget-light', 'admin_unique_layout.general-widget-light')->name('general-widget-light');
Route::view('dashboard-dark', 'admin_unique_layout.dark_dashboard')->name('dashboard-dark');
Route::view('e-commerce-dark', 'admin_unique_layout.e-commerce-dark')->name('e-commerce-dark');
Route::view('general-widget-dark', 'admin_unique_layout.general-widget-dark')->name('general-widget-dark');
Route::view('dashboard-box', 'admin_unique_layout.box_dashboard')->name('dashboard-box');
Route::view('e-commerce-box', 'admin_unique_layout.e-commerce-box')->name('e-commerce-box');
Route::view('general-widget-box', 'admin_unique_layout.general-widget-box')->name('general-widget-box');


//widgets
Route::view('general-widget', 'Widgets.general_widget')->name('general_widget');
Route::view('chart-widget', 'Widgets.chart_widget')->name('chart_widget');













//charts
Route::view('chart-apex', 'charts.chart_apex')->name('chart_apex');
Route::view('chart-sparkline', 'charts.chart_sparkline')->name('chart_sparkline');
Route::view('chart-flot', 'charts.chart_flot')->name('chart_flot');
Route::view('chart-knob', 'charts.chart_knob')->name('chart_knob');
Route::view('chart-morris', 'charts.chart_morris')->name('chart_morris');
Route::view('chartjs', 'charts.chartjs')->name('chartjs');
Route::view('chartist', 'charts.chartist')->name('chartist');
Route::view('chart-peity', 'charts.chart_peity')->name('chart_peity');

//landing_page
Route::view('landing-page', 'pages.landing_page')->name('landing_page');

Route::get('/error/{code}', function ($code) {
    abort($code);
});



Route::post('/log', [UserController::class, 'login'])->name('login');
Route::group(['middleware' => ['prevent-back-history', SetLocale::class]], function () {
    // Route::get('/', [UserController::class, 'home'])->name('home');
    Route::get('/index', [UserController::class, 'home'])->name('home');
    Route::get('/local/{ln}', function ($ln) {
        return redirect()->back()->with('local', $ln);
    });
    Route::get('/genTerm', [UserController::class, 'genTerm'])->name('genTerm');
    Route::get('/privacy', [UserController::class, 'privacy'])->name('privacy');
    Route::get('/book', [UserController::class, 'book'])->name('book');
    Route::post('contact_send', [Pages::class, 'contact_send']);
    Route::get('Userlogin', [UserController::class, 'Userlogin'])->name('Userlogin');
    Route::get('newsDetails/{id}', [UserController::class, 'newsDetails'])->name('newsDetails');

    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard')->middleware('isLoggedIn');
    Route::get('/dashboard-data', [UserController::class, 'getDashboardData'])->name('getDashboardData');


    Route::get('blog', [UserController::class, 'blogs'])->name('blog');

    Route::get('service', [UserController::class, 'service'])->name('service');

    Route::post('/comments/store', [UserController::class, 'Commentstore'])->name('comments.store');
    Route::post('/reactions', [UserController::class, 'Reactionstore']);
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard')->middleware('isLoggedIn');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');

    Route::get('/blog_details/{slug}', [UserController::class, 'blog_details'])->name('blog_details');
    Route::post('/blog/{id}/like', [BlogController::class, 'incrementLike'])->name('blog.like');
    Route::post('blog-comment', [UserController::class, 'blogCommentStore'])->name('blog-comment.store');
    Route::post('blog-comment-reply', [UserController::class, 'blogCommentReplyStore'])->name('blog-comment-reply.store');
    Route::get('search-blog-list', [UserController::class, 'searchBlogList'])->name('search-blog.list');
    Route::get('/signup', [UserController::class, 'signup'])->name('signup')->middleware('alreadyLoggedIn');
    Route::post('/reg', [UserController::class, 'registration']);
    Route::get('/contact', [UserController::class, 'contact'])->name('contact');
    Route::get('/about', [UserController::class, 'about'])->name('about');
    Route::get('/workUs', [UserController::class, 'workUs'])->name('workUs');
    Route::get('/faq', [UserController::class, 'faq'])->name('faq');
    Route::get('/userNotifications', [UserController::class, 'userNotifications'])->name('userNotifications');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [ChatController::class, 'getChatMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendChatMessage'])->name('chat.send');

    Route::get('/news', [UserController::class, 'news'])->name('news');
    Route::get('/segundaFase', [UserController::class, 'segundaFase'])->name('segundaFase');
    Route::get('/membership', [UserController::class, 'membership'])->name('membership');
    Route::get('/ownership', [UserController::class, 'ownership'])->name('ownership');
    Route::get('/land', [UserController::class, 'land'])->name('land');
    Route::get('/vacancy ', [UserController::class, 'vacancy'])->name('vacancy');

    Route::get('/gen_cards', [UserController::class, 'gen_cards'])->name('gen_cards')->middleware('isLoggedIn');
    Route::get('/gen_members_area', [UserController::class, 'gen_members_area'])->name('gen_members_area')->middleware('isLoggedIn');
    Route::get('/partners', [UserController::class, 'partners'])->name('partners')->middleware('isLoggedIn');
    Route::get('/events', [UserController::class, 'events'])->name('events')->middleware('isLoggedIn');
    Route::get('/verification', [UserController::class, 'verification'])->name('verification')->middleware('isLoggedIn');
    Route::get('/home', [UserController::class, 'home'])->name('home')->middleware('isLoggedIn');
});







Route::post('/forget_mail', [UserController::class, 'forget_mail'])->name('forget_mail');
Route::post('/sendResetPasswordLink', [UserController::class, 'sendResetPasswordLink'])->name('sendResetPasswordLink');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/ResetPasswordLoad', [UserController::class, 'ResetPasswordLoad'])->name('ResetPasswordLoad');
Route::post('/ResetPassword', [UserController::class, 'ResetPassword'])->name('ResetPassword');
Route::get('/get-subcategories/{category_id}', [ProductController::class, 'getSubcategories']);

Route::get('admin/unlock', [Admin::class, 'unlock'])->name('unlock')->middleware('AdminAlreadyLoggedIn');
Route::post('/update-mode', [Admin::class, 'updateMode']);
Route::get('/get-user-mode', [Admin::class, 'getUserMode'])->name('getUserMode');
Route::group(['prefix' => 'admin', 'middleware' => ['check.session', 'super.admin']], function () {

    Route::group(['middleware' => 'admin-prevent-back-history', SetLocale::class], function () {

        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'list'])->name('products.list'); // Product List
            Route::get('/create', [ProductController::class, 'create'])->name('products.create'); // Show Create Form
            Route::post('/', [ProductController::class, 'store'])->name('products.store'); // Store Product
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('products.edit'); // Show Edit Form
            Route::put('/{product}', [ProductController::class, 'update'])->name('products.update'); // Update Product
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy'); // Delete Product
            Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete'); // Bulk Delete
        });
         Route::get('/qrcode', [QRCodeController::class, 'index'])->name('qrcode.index')->middleware('AdminIsLoggedIn');
        Route::get('/destroy_qrcode/{id}', [QRCodeController::class, 'destroy'])->name('destroy');
        Route::post('/qrcode/generate', [QRCodeController::class, 'generateQrCode'])->name('qrcode.generate');
        Route::get('/qrcode/download/{data}', [QRCodeController::class, 'downloadQrCode'])->name('qrcode.download');
        // Testimonial routes
        Route::prefix('testimonials')->group(function () {
            Route::get('/', [TestimonialController::class, 'index'])->name('testimonials.index');
            Route::get('create', [TestimonialController::class, 'create'])->name('testimonials.create');
            Route::post('store', [TestimonialController::class, 'store'])->name('testimonials.store');
            Route::get('edit/{id}', [TestimonialController::class, 'edit'])->name('testimonials.edit');
            Route::post('update/{id}', [TestimonialController::class, 'update'])->name('testimonials.update');
            Route::delete('delete/{id}', [TestimonialController::class, 'destroy'])->name('testimonials.delete');
            Route::post('bulk-delete', [TestimonialController::class, 'bulkDelete'])->name('testimonials.bulk.delete');
        });

        // Portfolio routes
        Route::prefix('portfolios')->group(function () {
            Route::get('/', [PortfolioController::class, 'index'])->name('portfolios.index');
            Route::get('create', [PortfolioController::class, 'create'])->name('portfolios.create');
            Route::post('store', [PortfolioController::class, 'store'])->name('portfolios.store');
            Route::get('edit/{id}', [PortfolioController::class, 'edit'])->name('portfolios.edit');
            Route::post('update/{id}', [PortfolioController::class, 'update'])->name('portfolios.update');
            Route::delete('delete/{id}', [PortfolioController::class, 'destroy'])->name('portfolios.delete');
            Route::post('bulk-delete', [PortfolioController::class, 'bulkDelete'])->name('portfolios.bulk.delete');
        });

        Route::get('/get/messages', [Admin::class, 'getMessages'])->name('get.messages');

        Route::get('/get-notifications', [Admin::class, 'getNotifications'])->name('get.notifications');
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/messages', [ChatController::class, 'getChatMessages'])->name('chat.messages');
        Route::post('/chat/send', [ChatController::class, 'sendChatMessage'])->name('chat.send');
        Route::post('/chat/update-seen', [ChatController::class, 'updateSeen'])->name('chat.updateSeen');
        //icons
        Route::get('flag-icon', [Admin::class, 'flag_icon'])->name('flag_icon');
        Route::get('font-awesome', [Admin::class, 'font_awesome'])->name('font_awesome');
        Route::get('ico-icon', [Admin::class, 'ico_icon'])->name('ico_icon');
        Route::get('themify-icon', [Admin::class, 'themify_icon'])->name('themify_icon');
        Route::get('feather-icon', [Admin::class, 'feather_icon'])->name('feather_icon');
        Route::get('whether-icon', [Admin::class, 'whether_icon'])->name('whether_icon');

        Route::resource('banners', BannerController::class)->names('admin.banners');
        Route::post('banners/bulk-destroy', [BannerController::class, 'bulkDestroy'])->name('admin.banners.bulkDestroy');

        Route::get('/local/{ln}', function ($ln) {
            $language = Language::where('iso_code', $ln)->first();
            if (!$language) {
                $language = Language::where('default_language', 'on')->first();
                if (!$language) {
                    $language = Language::find(1);
                }
                $ln = $language->iso_code;
            }

            session(['local' => $ln]);
            App::setLocale($ln);
            return redirect()->back();
        });
        Route::prefix('events')->group(function () {
            Route::get('/', [EventsController::class, 'index'])->name('events.index')->middleware('AdminIsLoggedIn');
            Route::get('create', [EventsController::class, 'create'])->name('events.create')->middleware('AdminIsLoggedIn');
            Route::post('store', [EventsController::class, 'store'])->name('events.store');
            Route::get('edit/{uuid}', [EventsController::class, 'edit'])->name('events.edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{uuid}', [EventsController::class, 'update'])->name('events.update');
            Route::get('delete/{uuid}', [EventsController::class, 'delete'])->name('events.delete');
            Route::post('bulk-delete', [EventsController::class, 'bulkDelete'])->name('events.bulk.delete');
        });

        //file manager

        Route::get('/file-manager', [Admin::class, 'file_manager'])->name('file_manager');
        Route::post('/create-folder', [Admin::class, 'createFolder'])->name('create.folder');
        Route::post('/upload-file/{folderId}', [Admin::class, 'upload'])->name('upload.file');



        Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
            Route::get('index', [SupportTicketController::class, 'ticketIndex'])->name('index');
            Route::get('open', [SupportTicketController::class, 'ticketOpen'])->name('open');
            Route::get('show/{uuid}', [SupportTicketController::class, 'ticketShow'])->name('show')->middleware('AdminIsLoggedIn');
            Route::get('delete/{uuid}', [SupportTicketController::class, 'ticketDelete'])->name('delete');
            Route::post('change-ticket-status', [SupportTicketController::class, 'changeTicketStatus'])->name('changeTicketStatus');
            Route::post('message-store', [SupportTicketController::class, 'messageStore'])->name('messageStore');
            Route::post('bulk-delete', [SupportTicketController::class, 'bulkDelete'])->name('bulkDelete');
        });


        Route::group(['prefix' => 'forum', 'as' => 'forum.'], function () {
            Route::get('category-index', [ForumCategoryController::class, 'index'])->name('category.index');
            Route::post('category-store', [ForumCategoryController::class, 'store'])->name('category.store');
            Route::patch('category-update/{uuid}', [ForumCategoryController::class, 'update'])->name('category.update');
            Route::get('category-delete/{uuid}', [ForumCategoryController::class, 'delete'])->name('category.delete');
        });

        Route::group(['prefix' => 'news', 'as' => 'news.'], function () {
            Route::get('', [NewsController::class, 'index'])->name('index');
            Route::get('create', [NewsController::class, 'create'])->name('create'); // This matches 'audiolibros.crear'
            Route::post('store', [NewsController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NewsController::class, 'edit'])->name('edit'); // This matches 'audiolibros.edit'
            Route::post('update/{id}', [NewsController::class, 'update'])->name('update');
            Route::delete('/destroy/{news}', [NewsController::class, 'destroy'])->name('destroy');

            Route::post('/eliminar-multiple', [NewsController::class, 'bulkDelete'])->name('bulk-delete');
        });

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            //Start:: General Settings
            Route::get('general-settings', [SettingController::class, 'GeneralSetting'])->name('general_setting');
            Route::post('general-settings-update', [SettingController::class, 'GeneralSettingUpdate'])->name('general_setting.cms.update');

            Route::get('metas', [SettingController::class, 'metaIndex'])->name('meta.index');
            Route::get('meta/edit/{uuid}', [SettingController::class, 'editMeta'])->name('meta.edit');
            Route::post('meta/update/{uuid}', [SettingController::class, 'updateMeta'])->name('meta.update');

            // Route::get('site-share-content', [SettingController::class, 'siteShareContent'])->name('site-share-content');
            Route::get('map-api-key', [SettingController::class, 'mapApiKey'])->name('map-api-key');



            //Start:: Social Login Settings
            Route::get('social-login-settings', [SettingController::class, 'socialLoginSettings'])->name('social-login-settings');
            Route::post('social-settings-update', [SettingController::class, 'socialLoginSettingsUpdate'])->name('social-login-settings.update');



            //Start:: Currency Settings
            Route::group(['prefix' => 'currency', 'as' => 'currency.'], function () {
                Route::get('', [CurrencyController::class, 'index'])->name('index');
                Route::post('currency', [CurrencyController::class, 'store'])->name('store');
                Route::get('edit/{id}/{slug?}', [CurrencyController::class, 'edit'])->name('edit');
                Route::patch('update/{id}', [CurrencyController::class, 'update'])->name('update');
                Route::get('delete/{id}', [CurrencyController::class, 'delete'])->name('delete');
            });


            //Start:: Home Settings
            Route::get('theme-settings', [HomeSettingController::class, 'themeSettings'])->name('theme-setting');
            Route::get('section-settings', [HomeSettingController::class, 'sectionSettings'])->name('section-settings');
            Route::post('sectionSettingsStatusChange', [HomeSettingController::class, 'sectionSettingsStatusChange'])->name('sectionSettingsStatusChange');
            Route::get('banner-section-settings', [HomeSettingController::class, 'bannerSection'])->name('banner-section');
            Route::post('banner-section-settings', [HomeSettingController::class, 'bannerSectionUpdate'])->name('banner-section.update');
            Route::get('special-feature-section-settings', [HomeSettingController::class, 'specialFeatureSection'])->name('special-feature-section');
            Route::get('course-section-settings', [HomeSettingController::class, 'courseSection'])->name('course-section');
            Route::get('category-course-section-settings', [HomeSettingController::class, 'categoryCourseSection'])->name('category-course-section');
            Route::get('upcoming-course-section-settings', [HomeSettingController::class, 'upcomingCourseSection'])->name('upcoming-course-section');
            Route::get('product-section-settings', [HomeSettingController::class, 'productSection'])->name('product-section');
            Route::get('bundle-course-section-settings', [HomeSettingController::class, 'bundleCourseSection'])->name('bundle-course-section');
            Route::get('top-category-section-settings', [HomeSettingController::class, 'topCategorySection'])->name('top-category-section');

            Route::get('customer-say-section-settings', [HomeSettingController::class, 'customerSaySection'])->name('customer-say-section');
            Route::get('achievement-section-settings', [HomeSettingController::class, 'achievementSection'])->name('achievement-section');
            //End:: Home Settings

            //Start:: Mail Config
            Route::get('mail-configuration', [SettingController::class, 'mailConfiguration'])->name('mail-configuration');
            Route::post('send-test-mail', [SettingController::class, 'sendTestMail'])->name('send.test.mail');
            Route::post('save-setting', [SettingController::class, 'saveSetting'])->name('save.setting');
            //End:: Mail Config



            //Start:: FAQ Question & Answer
            Route::get('faq-settings', [SettingController::class, 'faqCMS'])->name('faq.cms');
            Route::get('faq-tab', [SettingController::class, 'faqTab'])->name('faq.tab');
            Route::get('faq-question-settings', [SettingController::class, 'faqQuestion'])->name('faq.question');
            Route::post('faq-question-settings', [SettingController::class, 'faqQuestionUpdate'])->name('faq.question.update');
            //End:: FAQ Question & Answer



            //Start:: Support Ticket
            Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.'], function () {
                Route::get('/', [SettingController::class, 'supportTicketCMS'])->name('cms');
                Route::get('question-answer', [SettingController::class, 'supportTicketQuesAns'])->name('question');
                Route::post('question-answer', [SettingController::class, 'supportTicketQuesAnsUpdate'])->name('question.update');
                Route::post('questionAnsDelete', [SettingController::class, 'questionAnsDelete'])->name('questionAnsDelete');

                Route::get('department', [SupportTicketController::class, 'Department'])->name('department');
                Route::post('department', [SupportTicketController::class, 'DepartmentStore'])->name('department.store');
                Route::delete('department-delete/{uuid}', [SupportTicketController::class, 'departmentDelete'])->name('department.delete');

                Route::get('priority', [SupportTicketController::class, 'Priority'])->name('priority');
                Route::post('priority', [SupportTicketController::class, 'PriorityStore'])->name('priority.store');
                Route::delete('priorities-delete/{uuid}', [SupportTicketController::class, 'priorityDelete'])->name('priority.delete');

                Route::get('services', [SupportTicketController::class, 'RelatedService'])->name('services');
                Route::post('services', [SupportTicketController::class, 'RelatedServiceStore'])->name('services.store');
                Route::delete('services-delete/{uuid}', [SupportTicketController::class, 'relatedServiceDelete'])->name('services.delete');
            });
            //End:: Support Ticket

            // Start:: Contact Us
            Route::get('contact-us-cms', [ContactUsController::class, 'contactUsCMS'])->name('contact.cms');
            // End:: Contact Us

            Route::get('payment-method', [SettingController::class, 'paymentMethod'])->name('payment_method_settings');

            //start:: Bank
            Route::group(['prefix' => 'bank'], function () {
                Route::get('/', [BankController::class, 'index'])->name('bank.index');
                Route::post('/store', [BankController::class, 'store'])->name('bank.store');
                Route::get('/edit/{id}', [BankController::class, 'edit'])->name('bank.edit');
                Route::patch('/update/{id}', [BankController::class, 'update'])->name('bank.update');
                Route::get('/status/{id}', [BankController::class, 'status'])->name('bank.status');
                Route::delete('delete/{id}', [BankController::class, 'delete'])->name('bank.delete');
            });

            // Start:: About Us
            Route::group(['prefix' => 'about', 'as' => 'about.'], function () {
                Route::get('about-us-gallery-area', [AboutUsController::class, 'galleryArea'])->name('gallery-area');
                Route::post('about-us-gallery-area', [AboutUsController::class, 'galleryAreaUpdate'])->name('gallery-area.update');
                Route::post('gallery/delete', [AboutUsController::class, 'deleteGalleryImage'])->name('gallery.delete');


                Route::get('about-us-our-history', [AboutUsController::class, 'ourHistory'])->name('our-history');
                Route::post('about-us-our-history', [AboutUsController::class, 'ourHistoryUpdate'])->name('our-history.update');

                Route::get('about-us-upgrade-skill', [AboutUsController::class, 'upgradeSkill'])->name('upgrade-skill');
                Route::post('about-us-upgrade-skill', [AboutUsController::class, 'upgradeSkillUpdate'])->name('upgrade-skill.update');
                Route::post('skillDelete', [AboutUsController::class, 'deleteSkill'])->name('skill.delete');


                Route::get('about-us-team-member', [AboutUsController::class, 'teamMember'])->name('team-member');
                Route::post('about-us-team-member', [AboutUsController::class, 'teamMemberUpdate'])->name('team-member.update');

                Route::get('about-us-instructor-support', [AboutUsController::class, 'instructorSupport'])->name('instructor-support');
                Route::post('about-us-instructor-support', [AboutUsController::class, 'instructorSupportUpdate'])->name('instructor-support.update');

                Route::get('about-us-client', [AboutUsController::class, 'client'])->name('client');
                Route::post('about-us-client', [AboutUsController::class, 'clientUpdate'])->name('client.update');
            });
            // End:: About Us

            Route::group(['prefix' => 'locations', 'as' => 'location.'], function () {
                Route::get('country', [LocationController::class, 'countryIndex'])->name('country.index');
                Route::post('country', [LocationController::class, 'countryStore'])->name('country.store');
                Route::get('country/{id}/{slug?}', [LocationController::class, 'countryEdit'])->name('country.edit');
                Route::patch('country/{id}', [LocationController::class, 'countryUpdate'])->name('country.update');
                Route::delete('country/delete/{id}', [LocationController::class, 'countryDelete'])->name('country.delete');

                Route::get('state', [LocationController::class, 'stateIndex'])->name('state.index');
                Route::post('state', [LocationController::class, 'stateStore'])->name('state.store');
                Route::get('state/{id}/{slug?}', [LocationController::class, 'stateEdit'])->name('state.edit');
                Route::patch('state/{id}', [LocationController::class, 'stateUpdate'])->name('state.update');
                Route::delete('state/delete/{id}', [LocationController::class, 'stateDelete'])->name('state.delete');

                Route::get('city', [LocationController::class, 'cityIndex'])->name('city.index');
                Route::post('city', [LocationController::class, 'cityStore'])->name('city.store');
                Route::get('city/{id}/{slug?}', [LocationController::class, 'cityEdit'])->name('city.edit');
                Route::patch('city/{id}', [LocationController::class, 'cityUpdate'])->name('city.update');
                Route::delete('city/delete/{id}', [LocationController::class, 'cityDelete'])->name('city.delete');
            });
        });
        Route::get('notification-url/{uuid}', [Admin::class, 'notificationUrl'])->name('notification.url');
        Route::post('mark-all-as-read', [Admin::class, 'markAllAsReadNotification'])->name('notification.all-read');
        Route::prefix('language')->group(function () {
            Route::get('/', [LanguageController::class, 'index'])->name('language.index')->middleware('AdminIsLoggedIn');
            Route::get('create', [LanguageController::class, 'create'])->name('language.create')->middleware('AdminIsLoggedIn');
            Route::post('store', [LanguageController::class, 'store'])->name('language.store');
            Route::get('edit/{id}/{iso_code?}', [LanguageController::class, 'edit'])->name('language.edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{id}', [LanguageController::class, 'update'])->name('language.update');
            Route::get('translate/{id}', [LanguageController::class, 'translateLanguage'])->name('language.translate')->middleware('AdminIsLoggedIn');
            Route::post('update-translate/{id}', [LanguageController::class, 'updateTranslate'])->name('update.translate');
            Route::get('delete/{id}', [LanguageController::class, 'delete'])->name('language.delete');
            Route::post('import', [LanguageController::class, 'import'])->name('language.import');
            Route::post('update-language/{id}', [LanguageController::class, 'updateLanguage'])->name('update-language');
        });

        Route::group(['prefix' => 'role', 'as' => 'role.'], function () {
            Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('AdminIsLoggedIn');
            Route::get('create', [RoleController::class, 'create'])->name('create')->middleware('AdminIsLoggedIn');
            Route::post('store', [RoleController::class, 'store'])->name('store');
            Route::get('edit/{id}', [RoleController::class, 'edit'])->name('edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{id}', [RoleController::class, 'update'])->name('update');
            Route::get('delete/{id}', [RoleController::class, 'delete'])->name('delete');
        });
        Route::prefix('tag')->group(function () {
            Route::get('/', [TagController::class, 'index'])->name('tag.index')->middleware('AdminIsLoggedIn');
            Route::get('create', [TagController::class, 'create'])->name('tag.create')->middleware('AdminIsLoggedIn');
            Route::post('store', [TagController::class, 'store'])->name('tag.store');
            Route::get('edit/{uuid}', [TagController::class, 'edit'])->name('tag.edit')->middleware('AdminIsLoggedIn');
            Route::patch('update/{uuid}', [TagController::class, 'update'])->name('tag.update');
            Route::get('delete/{uuid}', [TagController::class, 'delete'])->name('tag.delete');
        });
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category.index')->middleware('AdminIsLoggedIn');
            Route::get('create', [CategoryController::class, 'create'])->name('category.create')->middleware('AdminIsLoggedIn');
            Route::post('store', [CategoryController::class, 'store'])->name('category.store');
            Route::get('edit/{uuid}', [CategoryController::class, 'edit'])->name('category.edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{uuid}', [CategoryController::class, 'update'])->name('category.update');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');

            Route::post('bulk-delete', [CategoryController::class, 'bulkDelete'])->name('category.bulk.delete');
        });


        Route::prefix('subcategory')->group(function () {
            Route::get('/', [SubcategoryController::class, 'index'])->name('subcategory.index');
            Route::get('create', [SubcategoryController::class, 'create'])->name('subcategory.create');
            Route::post('store', [SubcategoryController::class, 'store'])->name('subcategory.store');
            Route::get('edit/{uuid}', [SubcategoryController::class, 'edit'])->name('subcategory.edit');
            Route::post('update/{uuid}', [SubcategoryController::class, 'update'])->name('subcategory.update');
            Route::delete('delete/{uuid}', [SubcategoryController::class, 'delete'])->name('subcategory.delete');
            Route::post('bulk-delete', [SubcategoryController::class, 'bulkDelete'])->name('subcategory.bulk.delete');
        });


        Route::get('childcategory', [SubcategoryController::class, 'childcategory'])->name('childcategory')->middleware('AdminIsLoggedIn');
        Route::prefix('gallery')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('gallery.index')->middleware('AdminIsLoggedIn');
            Route::get('create', [MediaController::class, 'create'])->name('gallery.create')->middleware('AdminIsLoggedIn');
            Route::post('store', [MediaController::class, 'store'])->name('gallery.store');
            Route::get('edit/{id}', [MediaController::class, 'edit'])->name('gallery.edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{id}', [MediaController::class, 'update'])->name('gallery.update');
            Route::get('delete/{id}', [MediaController::class, 'delete'])->name('gallery.delete');
            Route::post('bulk-delete', [MediaController::class, 'bulkDelete'])->name('gallery.bulk.delete');
        });

        Route::group(['prefix' => 'blog', 'as' => 'blog.'], function () {
            Route::get('/', [BlogController::class, 'index'])->name('index')->middleware('AdminIsLoggedIn');
            Route::get('create', [BlogController::class, 'create'])->name('create')->middleware('AdminIsLoggedIn');
            Route::post('store', [BlogController::class, 'store'])->name('store');
            Route::get('edit/{uuid}', [BlogController::class, 'edit'])->name('edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{uuid}', [BlogController::class, 'update'])->name('update');
            Route::get('delete/{uuid}', [BlogController::class, 'delete'])->name('delete');
            Route::get('blog-comment-list', [BlogController::class, 'blogCommentList'])->name('blog-comment-list')->middleware('AdminIsLoggedIn');
            Route::post('change-blog-comment-status', [BlogController::class, 'changeBlogCommentStatus'])->name('changeBlogCommentStatus');
            Route::get('blog-comment-delete/{id}', [BlogController::class, 'blogCommentDelete'])->name('blogComment.delete');
            Route::post('blog-comment-bulk-delete', [BlogController::class, 'bulkDeleteComments'])->name('blogComment.bulkDelete'); // Unique name for blog comment bulk delete
            Route::post('bulk-delete', [BlogController::class, 'bulkDelete'])->name('bulkDelete'); // Fixed bulk delete route

            Route::get('blog-category-index', [BlogCategoryController::class, 'index'])->name('blog-category.index')->middleware('AdminIsLoggedIn');
            Route::post('blog-category-store', [BlogCategoryController::class, 'store'])->name('blog-category.store');
            Route::patch('blog-category-update/{uuid}', [BlogCategoryController::class, 'update'])->name('blog-category.update');
            Route::get('blog-category-delete/{uuid}', [BlogCategoryController::class, 'delete'])->name('blog-category.delete');
            Route::post('blog-category-bulk-delete', [BlogCategoryController::class, 'bulkDeleteBlogCategory'])->name('bulkDeleteBlogCategory');
        });







        Route::get('dashboard', [Admin::class, 'dashboard'])->name('dashboard')->middleware('AdminIsLoggedIn');

        Route::get('/edit_profile', [Admin::class, 'edit_profile'])->name('edit_profile')->middleware('AdminIsLoggedIn');
        Route::post('update_profile', [Admin::class, 'update_profile'])->name('update_profile')->middleware('AdminIsLoggedIn');


        Route::get('/change_password', [Admin::class, 'change_password'])->name('change_password')->middleware('AdminIsLoggedIn');
        Route::post('/update_password', [Admin::class, 'update_password'])->name('update_password')->middleware('AdminIsLoggedIn');

        Route::get('pages', [Pages::class, 'pages_list'])->middleware('AdminIsLoggedIn');
        Route::get('add', [Pages::class, 'add'])->middleware('AdminIsLoggedIn');
        Route::get('edit/{id}', [Pages::class, 'edit'])->middleware('AdminIsLoggedIn');
        Route::post('pages/add_edit', [Pages::class, 'addnew'])->middleware('AdminIsLoggedIn');
        Route::delete('pages/delete/{id}', [Pages::class, 'delete'])->middleware('AdminIsLoggedIn');
        Route::post('pages/bulk_delete', [Pages::class, 'bulkDelete'])->middleware('AdminIsLoggedIn');


        Route::get('users', [Admin::class, 'users'])->name('users')->middleware('AdminIsLoggedIn');

        Route::get('user/edit/{id}', [Admin::class, 'edit_user'])->name('edit_user')->middleware('AdminIsLoggedIn');
        Route::post('update_user', [Admin::class, 'update_user'])->name('update_user')->middleware('AdminIsLoggedIn');

        Route::get('add_user', [Admin::class, 'add_user'])->middleware('AdminIsLoggedIn');
        Route::post('save_user', [Admin::class, 'save_user'])->middleware('AdminIsLoggedIn');

        Route::get('user/delete_user/{id}', [Admin::class, 'delete_user']);
        Route::post('user/bulk_delete', [Admin::class, 'bulkDelete'])->name('user.bulkDelete');


        Route::name('mail-templates.')->prefix('mail-templates')->group(function () {
            Route::get('/', [MailTemplateController::class, 'index'])->name('index')->middleware('AdminIsLoggedIn');
            Route::get('add', [MailTemplateController::class, 'add'])->name('add')->middleware('AdminIsLoggedIn');
            Route::post('save', [MailTemplateController::class, 'save'])->name('save');
            Route::get('edit/{id}', [MailTemplateController::class, 'edit'])->name('edit')->middleware('AdminIsLoggedIn');
            Route::post('update/{id}', [MailTemplateController::class, 'update'])->name('update');
            Route::post('bulk-delete', [MailTemplateController::class, 'bulkDelete'])->middleware('AdminIsLoggedIn');
        });
        Route::get('email-application', [EmailAppController::class, 'index'])->name('index')->middleware('AdminIsLoggedIn');
        Route::post('sendMessage', [EmailAppController::class, 'sendMessage'])->name('sendMessage');
        Route::post('sendMail/{id}', [EmailAppController::class, 'sendMail'])->name('sendMail');
        Route::get('email-compose', [EmailAppController::class, 'compose'])->name('compose')->middleware('AdminIsLoggedIn');
    });

});
// Public routes (accessible without authentication)
Route::get('/admin/login', [Admin::class, 'admin'])->name('admin')->middleware('AdminAlreadyLoggedIn');
Route::get('/', [Admin::class, 'admin'])->name('admin')->middleware('AdminAlreadyLoggedIn');
Route::post('/admin/log', [Admin::class, 'login'])->name('login');
Route::get('/admin/forget_password', [Admin::class, 'forget_password'])->name('forget_password');
Route::post('/admin/unlocked', [Admin::class, 'unlocked'])->name('unlocked');
Route::get('/admin/signout', [Admin::class, 'logout'])->name('logout');
Route::get('facebook', [FacebookSocialiteController::class, 'facebookRedirect']);
Route::get('callback/facebook', [FacebookSocialiteController::class, 'loginWithFacebook']);
Route::get('google', [GoogleController::class, 'redirectToGoogle']);
Route::get('callback/google', [GoogleController::class, 'handleGoogleCallback']);
