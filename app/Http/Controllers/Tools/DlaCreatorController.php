<?php
namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AuthProvider\SourceUrlHandler;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Closure;

class DlaCreatorController extends Controller
{
    public function index()
    {
        $course = Course::findBySlug('digital-lead-academy') or abort(404);
        $tool = 'Creator';

        return view('tools.dla-creator.index', compact('course', 'tool'));
    }
}
