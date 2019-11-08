<?php
namespace App\Http\Controllers;

use Closure;
use Carbon;
use Route;
use App\Models\Course;
use App\Models\News;
use App\Models\NicheDetective\Niche;
use App\Models\Test;
use App\Models\Certificate;
use Sitemap;

class SitemapsController extends Controller
{

    public function isBot()
    {
        return (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']));
    }

    public function index()
    {

        Sitemap::addSitemap(route('sitemap.general'));
        if (env('APP_ENV') === 'testing') {
            Sitemap::addSitemap(route('sitemap.niches'));
            Sitemap::addSitemap(route('sitemap.courses'));
            Sitemap::addSitemap(route('sitemap.news'));
        }
        Sitemap::addSitemap(route('sitemap.legal'));
        Sitemap::addSitemap(route('sitemap.career'));

        if ($this->isBot()) {
            Sitemap::addSitemap(url('/blog/sitemap.xml'));
            return Sitemap::renderSitemapIndex()->header('Content-Type', 'text/xml');
        }

        Sitemap::addSitemap(url('/blog/sitemap'));
        $index = Sitemap::index();

        return view('pages.sitemap.index', compact('index'));
    }

    public function general()
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (!str_contains($route->getPrefix(), ['admin', '_debugbar', 'api', 'webhook', 'legal', 'track']) && !str_contains($route->uri(), ['}', 'career/']) && $route->methods[0] != "POST") {
                Sitemap::addTag(route($route->getAction()['as']), Carbon\Carbon::today(), 'daily', '0.8');
            }
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }

    public function niches()
    {
        if (env('APP_ENV') !== 'testing') {
            abort(404);
        }
        $niches = Niche::all();
        foreach ($niches as $niche) {
            Sitemap::addTag(route('niche-detail', $niche), $niche->updated_at, 'daily', '0.8');
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }

    public function courses()
    {
        if (env('APP_ENV') !== 'testing') {
            abort(404);
        }
        $courses = Course::enabled()->get();

        foreach ($courses as $course) {
            Sitemap::addTag(route('course', $course->slug), $course->updated_at, 'daily', '0.8');
            Sitemap::addTag(route('enroll', $course->slug), $course->updated_at, 'daily', '0.8');
            Sitemap::addTag(route('front.badges.index', $course->slug), $course->updated_at, 'daily', '0.8');
            foreach ($course->badges()->get() as $badge) {
                Sitemap::addTag(route('front.badges.request', [$course->slug, $badge]), $badge->updated_at, 'daily', '0.8');
            }
            $this->getModules($course);
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }

    private function getModules($course)
    {
        $modules = $course->modules()->enabled()->get();
        foreach ($modules as $module) {
            Sitemap::addTag(route('module', [$course->slug, $module->slug]), $module->updated_at, 'daily', '0.8');
            $lessons = $module->lessons()->enabled()->get();
            foreach ($lessons as $lesson) {
                foreach (Test::where('after_lesson_id', $lesson->id)->get() as $test) {
                    Sitemap::addTag(route('test', [$course->slug, $module->slug, $test]), $test->updated_at, 'daily', '0.8');

                    foreach (Certificate::where('id', $test->certificate_id)->get() as $cert) {
                        Sitemap::addTag(route('test-certificate', [$course->slug, $module->slug, $test]), $cert->updated_at, 'daily', '0.8');
                    }
                }
                Sitemap::addTag(route('lesson', [$course->slug, $module->slug, $lesson->slug]), $lesson->updated_at, 'daily', '0.8');
            }
        }
    }

    public function news()
    {
        if (env('APP_ENV') !== 'testing') {
            abort(404);
        }
        $newsStories = News::all();
        foreach ($newsStories as $news) {
            Sitemap::addTag(route('news-article', $news->slug), $news->updated_at, 'daily', '0.8');
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }

    public function legal()
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (str_contains($route->getPrefix(), ['legal']) && !str_contains($route->uri(), '}') && $route->methods[0] != "POST") {
                Sitemap::addTag(route($route->getAction()['as']), Carbon\Carbon::today(), 'daily', '0.8');
            }
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }

    public function career()
    {
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (str_contains($route->getPrefix(), ['career']) && !str_contains($route->uri(), '}') && $route->methods[0] != "POST") {
                Sitemap::addTag(route($route->getAction()['as']), Carbon\Carbon::today(), 'daily', '0.8');
            }
        }

        if ($this->isBot()) {
            return Sitemap::renderSitemap()->header('Content-Type', 'text/xml');
        }

        $index = Sitemap::renderSitemap();

        return view('pages.sitemap.index', compact('index'));
    }
}
