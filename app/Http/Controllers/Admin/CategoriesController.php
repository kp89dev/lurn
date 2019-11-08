<?php
namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Course\StoreCategoryRequest;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class CategoriesController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin.role.auth:categories,read')
            ->only('index');
        $this->middleware('admin.role.auth:categories,write')
            ->only('create', 'store', 'edit', 'update', 'destroy', 'update-status');
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        if ($request->category) {
            return redirect()->route('admin', $request->only('category'));
        } else {
            return view('admin.category.index')
                ->withCategories(Category::simplePaginate(20));
        }
    }

    /**
     * @return View
     */
    public function create()
    {
        $category = new Category;
        $action = route('categories.store');
        $method = '';

        return view('admin.category.create-edit', compact('category', 'action', 'method'));
    }

    /**
     * @param StoreCategoryRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->only('name');
        $category = Category::create($data);

        $this->storeThumbnail($category);

        return redirect()->route('categories.index')->with('alert-success', 'Category succesfully added');
    }

    /**
     * @param Category $category
     * @return View
     */
    public function edit(Category $category)
    {
        $action = route('categories.update', $category->id);
        $method = method_field('PUT');

        return view('admin.category.create-edit', compact('category', 'action', 'method'));
    }

    /**
     * @param Category        $category
     * @param StoreCategoryRequest $request
     * @return RedirectResponse
     */
    public function update(Category $category, StoreCategoryRequest $request)
    {
        $category->update($request->only('name'));
        $this->storeThumbnail($category);

        return redirect()->route('categories.index')->with('alert-success', 'Category succesfully modified');
    }

    /**
     * @param Category $category
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $category->delete();

        request()->session()->flash('alert-success', 'Category was successfully deleted!');

        return redirect()->back();
    }

    /**
     * @param Category $category
     */
    protected function storeThumbnail(Category $category)
    {
        if ($thumbnail = request()->file('thumbnail')) {
            $name = sprintf('thumbnail-%s.%s', str_random(), $thumbnail->extension());
            $resized = Image::make($thumbnail)->fit(640, 360)->stream();

            Storage::disk('static')->put("categories/$category->id/$name", (string) $resized);

            $category->update(['thumbnail' => $name]);
        }
    }
}
