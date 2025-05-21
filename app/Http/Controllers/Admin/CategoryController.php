<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use  ImageSaveTrait, General;

    protected $model;
    public function __construct(Category $category)
    {
        $this->model = new Crud($category);
    }
    public function getNameCategoryById($id)
{
    $category = Category::find($id);

    if (!$category) {
        return response()->json(['message' => 'Category not found'], 404);
    }

    return response()->json(['name' => $category->name]);
}

    public function categories()
    {
        return response()->json(Category::all());
    }
    public function subcategories($category)
    {
        return response()->json(Subcategory::where('parent_category_id', $category)->paginate(10));
    }
    public function index()
    {
        if (Session::has('LoggedIn')) {


            $data['user_session'] = User::where('id', Session::get('LoggedIn'))->first();

            $data['title'] = 'Manage Category';
            $data['categories'] = Category::all();
            return view('admin.category.index', $data);
        }
    }

    public function create()
    {

        if (Session::has('LoggedIn')) {


            $data['user_session'] = User::where('id', Session::get('LoggedIn'))->first();
            $data['title'] = 'Add Category';
            return view('admin.category.create', $data);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            // 'name' => 'required|string|max:255',
            // 'image' => 'nullable|image|max:1024',
            // 'meta_title' => 'nullable|string|max:255',
            // 'meta_description' => 'nullable|string|max:255',
            // 'meta_keywords' => 'nullable|string|max:255',
            // 'og_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'is_feature' => $request->has('is_feature') ? 'yes' : 'no',
            'slug' => Str::slug($request->name),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        if ($request->hasFile('image')) {
            $attribute = $request->file('image');
            $destination = 'category';
            $file_name = time() . '-' . Str::random(10) . '.' . $attribute->getClientOriginalExtension();
            $attribute->move(public_path('uploads/' . $destination), $file_name);
            $data['image'] = 'uploads/' . $destination . '/' . $file_name;
        }

        if ($request->hasFile('og_image')) {
            $attribute = $request->file('og_image');
            $destination = 'meta';
            $file_name = time() . '-' . Str::random(10) . '.' . $attribute->getClientOriginalExtension();
            $attribute->move(public_path('uploads/' . $destination), $file_name);
            $data['og_image'] = 'uploads/' . $destination . '/' . $file_name;
        }

        try {
            $this->model->create($data);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error creating category.'], 500);
        }
    }

    public function edit($uuid)
    {

        if (Session::has('LoggedIn')) {
            $data['user_session'] = User::where('id', Session::get('LoggedIn'))->first();
            $data['title'] = 'Edit Category';
            $data['category'] = $this->model->getRecordByUuid($uuid);
            return view('admin.category.edit', $data);
        }
    }

    public function update(Request $request, $uuid)
    {

        $category = $this->model->getRecordByUuid($uuid);
        $image = $category->image;
        if ($request->hasFile('image')) {
            $this->deleteFile($category->image);
            // Handle new image upload
            $attribute = $request->file('image');
            $destination = 'category';

            // Generate unique filename
            $file_name = time() . '-' . Str::random(10) . '.' . $attribute->getClientOriginalExtension();
            // Move uploaded file to the destination directory
            $attribute->move(public_path('uploads/' . $destination), $file_name);
            // Update image path
            $image = 'uploads/' . $destination . '/' . $file_name;
        }

        $data = [
            'name' => $request->name,
            'is_feature' => $request->is_feature ? 'yes' : 'no',
            'slug' => getSlug($request->name),
            'image' => $image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
        ];

        // Handle OG image upload
        if ($request->hasFile('og_image')) {
            $attribute = $request->file('og_image');
            $destination = 'meta';

            // Generate unique filename
            $file_name = time() . '-' . Str::random(10) . '.' . $attribute->getClientOriginalExtension();
            // Move uploaded file to the destination directory
            $attribute->move(public_path('uploads/' . $destination), $file_name);
            // Update og_image path
            $data['og_image'] = 'uploads/' . $destination . '/' . $file_name;
        }

        $this->model->updateByUuid($data, $uuid); // update category
        return response()->json(['success' => true]);
    }

   public function delete($id)
    {
        try {
            $category = Category::where('id', $id)->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada.'
                ], 404);
            }


            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en delete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría.'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            Log::info('Datos recibidos para bulk delete:', $request->all());

            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:categories,uuid',
            ]);

            // Set category_id to NULL for products and subcategories
            $categories = Category::whereIn('id', $request->ids)->get();


            Category::whereIn('id', $request->ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categorías eliminadas correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error en bulkDelete: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar las categorías seleccionadas.'
            ], 500);
        }
    }
}
