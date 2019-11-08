<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Course\StoreNewsRequest;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:news,read')
            ->only('index');
        $this->middleware('admin.role.auth:news,write')
            ->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * @return View
     */
    public function index()
    {
        $news = News::orderBy('id', 'desc')->paginate(10);

        return view('admin.news.index', compact('news'));
    }

    /**
     * @return View
     */
    public function create()
    {
        $article = new News;
        $action = route('news.store');
        $method = '';

        return view('admin.news.create-edit', compact('article', 'action', 'method'));
    }

    /**
     * @param StoreNewsRequest $request
     * @return RedirectResponse
     */
    public function store(StoreNewsRequest $request)
    {
        $data = $request->only('title', 'content');

        News::create($data);

        return redirect()->route('news.index')->with('alert-success', 'News succesfully added');
    }

    /**
     * @param News $news
     * @return View
     */
    public function edit(News $news)
    {
        $action = route('news.update', $news->id);
        $method = method_field('PUT');

        return view('admin.news.create-edit', compact('action', 'method'))->withArticle($news);
    }

    /**
     * @param News             $news
     * @param StoreNewsRequest $request
     * @return RedirectResponse
     */
    public function update(News $news, StoreNewsRequest $request)
    {
        $news->fill($request->only('title', 'content'));
        $news->save();

        return redirect()->route('news.index')->with('alert-success', 'News successfully modified');
    }

    /**
     * @param News $news
     * @return RedirectResponse
     */
    public function destroy(News $news)
    {
        $news->delete();

        request()->session()->flash('alert-success', 'News were successfully deleted!');

        return redirect()->back();
    }
}
