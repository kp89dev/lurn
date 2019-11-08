<?php
namespace App\Http\Controllers\Admin\SEO;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\RedirectResponse;
use App\Models\SEO;
use App\Models\SeoDefault;
use App\Models\SeoCourse;

class SEOController extends Controller
{

    public function index()
    {

        $action = route('seo.update.default');
        $seo = new SEO;
        $seoDefaults = $seo->getSeoDefaults();
        return view('admin.seo.index', compact('action', 'seoDefaults'));
    }

    public function updateDefault(Request $request)
    {

        $seoDefaults = new SeoDefault;
        $seoDefaults->fill($request->all());
        $seoDefaults->save();
        return redirect()->route('seo.index')->with('alert-success', 'SEO Settings Updated');
    }

    public function updateCourse(Request $request)
    {
        $courseSeo = SeoCourse::firstOrNew(array('course_id' => $request->course));
        $courseSeo->fill($request->except('course'));
        $courseSeo->robots = ($request->robots ?: 0);
        $courseSeo->og_enabled = ($request->og_enabled ?: 0);
        $courseSeo->twitter_enabled = ($request->twitter_enabled ?: 0);
        $courseSeo->save();
        return redirect()->back()->with('alert-success', 'Course SEO Settings Updated');
    }
}
