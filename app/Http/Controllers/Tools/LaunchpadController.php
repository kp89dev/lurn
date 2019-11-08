<?php
namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AuthProvider\SourceUrlHandler;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Closure;

class LaunchpadController extends Controller
{

    public function index()
    {
        /**
         * @todo this needs to be made more global, what if we want to attach to a different course
         *       or the slug changes ?
         */
        $course = Course::findBySlug('inbox-blueprint') or abort(404);
        $tool = 'Launchpad';

        return view('tools.launchpad.index', compact('course', 'tool'));
    }
}
