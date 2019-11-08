<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseLike;
use Illuminate\Http\Request;

class ThumbsUpController extends Controller
{
    public function click(Request $request)
    {
        CourseLike::updateOrCreate($request->only('course_id', 'user_id'), $request->all());

        return response()->json(['success' => true]);
    }
}
