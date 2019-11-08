<?php
namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\News;
use Illuminate\View\View;
use Closure;

class NewsController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    /**
     * @return View
     */
    public function index()
    {
        $news = News::orderBy('id', 'desc')->paginate(10);
        $ads = new Ad;
        $adFirst = $ads->getByLocationAndPosition('dashboard', 'first');
        $adSecond = $ads->getByLocationAndPosition('dashboard', 'second');

        return view('pages.news.index', compact('news', 'adFirst', 'adSecond'));
    }

    /**
     * @param $slug
     * @return View
     */
    public function show($slug)
    {
        $news = News::findBySlug($slug, false) or abort(404);
        $otherNews = News::where('id', '!=', $news->id)->orderBy('id', 'desc')->take(5)->get();

        return view('pages.news.show', compact('news', 'otherNews'));
    }
}
