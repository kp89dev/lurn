<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseSearchController extends Controller
{

    public function index(Request $request)
    {
        $searchTerm = trim($request->get('term'));
        $courses = Course::searchByIdOrTitle($searchTerm, 10)->get();

        return response()->json($courses);
    }

    public function search(Request $request)
    {
        $searchTerm = trim($request->get('term'));
        $courses = Course::searchByIdOrTitle($searchTerm, -1)->orderBy('id', 'DESC')->pluck('id');
        return redirect('admin/courses')
        ->with('courses', $courses)
        ->with('filter', $searchTerm);
    }
}
