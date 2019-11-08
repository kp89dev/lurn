<?php
namespace App\Http\Controllers\Admin\Homepage;

use App\Models\Course;
use App\Models\CourseFeature;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class HomepageController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:homepage,read')
            ->only('index');
        $this->middleware('admin.role.auth:homepage,write')
            ->only('create', 'store', 'edit', 'update', 'destroy', 'storeFeatured');
    }

    public function index()
    {
        $courses      = Course::where('status', 1)->where('purchasable', 1)->get();
        $featured     = CourseFeature::where('free_bootcamp', 0)->orderBy('order')->get();
        $freeBootcamp = CourseFeature::where('free_bootcamp', 1)->orderBy('order')->get();

        return view('admin.homepage.index', compact('courses', 'featured', 'freeBootcamp'));
    }

    public function storeFeatured(Request $request)
    {
        (new CourseFeature())->setFeatured(
            $request->only('featured1', 'featured2', 'featured3', 'featured4'),
            $request->freeBootcamp
        );

        return redirect()->back()->with('alert-success', 'Featured Courses Succesfully Updated');
    }
}
