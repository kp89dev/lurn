<?php
namespace App\Http\Controllers\Classroom;

use App\Http\Controllers\Controller;
use App\Http\Requests\Badge\BadgeRequest;
use App\Models\Badge;
use Illuminate\Http\Request;
use Closure;

class BadgesController extends Controller
{
    public function __construct()
    {
        $this->middleware('resolve.resources');
        $this->middleware('enrollment.check');
        
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $course = $request->course;

        $badges = $course->badges->where('status', 1)
            ->whereNotIn('id', user()->acquiredBadges->pluck('id'));

        if (! $badges->count()) {
            abort(404);
        }

        return view('badges.index', compact('course', 'badges'));
    }

    public function request(Request $request, $courseSlug, Badge $badge)
    {
        $course = $request->course;
        $userHasBadge = false;

        return view('badges.request', compact('badge', 'course', 'userHasBadge'));
    }

    public function requestStore(BadgeRequest $request, $courseSlug, Badge $badge)
    {
        $badgeRequest = user()->badgeRequests()->create([
            'comment'  => $request->get('comment'),
            'badge_id' => $badge->id
        ]);

        $badgeRequestsPath = sprintf('courses/%d/badge-requests/%d', request('course')->id, user()->id);

        foreach ($request->proof as $file) {
            $storedFilePath = $file->store($badgeRequestsPath, 'private');

            $badgeRequest->files()->create([
                'file_path' => $storedFilePath,
            ]);
        }

        return redirect()->back()->with([
            'success' => 'Your request has been successfully submitted. ' .
                         'Please wait while we review your request.'
        ]);
    }
}
