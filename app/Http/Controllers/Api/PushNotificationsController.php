<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\News;
use Carbon\Carbon;
use App\Models\User;

class PushNotificationsController extends Controller
{

    public $pushNotifications;

    /**
     * Request $request
     *
     * @return JsonResponse
     */
    public function unreadPushNotifications(Request $request)
    {

        $browserNow = Carbon::now()->addMinutes($request->get('browserTZOffset'));

        $browserNow->setTimezone(($request->dst == true ? 1 : 0));
        if (user()) {
            $pushNotifications = PushNotifications::whereDoesntHave('user', function ($query) {
                $query->where('users.id', '=', user()->id);
            })
                ->where('start_utc', '<', $browserNow)
                ->where('end_utc', '>', $browserNow)->get();

            if (count($pushNotifications) > 0) {
                $this->excludeUserCourses($pushNotifications);
            }
        }
        else {
            $recentlyViewed = collect();
            if (session()->has('pushViewed')) {
                foreach (session()->get('pushViewed') as $k => $v) {
                    $recentlyViewed->push($v);
                }
            }
            $pushNotifications = PushNotifications::whereNotIn('id', $recentlyViewed)
                ->where('start_utc', '<', $browserNow)
                ->where('end_utc', '>', $browserNow)
                ->where('all_visitors', 1)
                ->orderby('end_utc', 'asc')
                ->take(1)
                ->get();
            if (count($pushNotifications) > 0) {
                $this->pushNotifications = $pushNotifications;
            }
        }

        return response()->json($this->pushNotifications);
    }

    public function excludeUserCourses($pushNotifications)
    {
        $userCourseIds = user()->courseSubscriptions()
            ->select('course_id')
            ->whereUserId(user()->id)
            ->whereNull('cancelled_at')
            ->get();

        $userCourseSlugs = collect();

        foreach ($userCourseIds as $userCourseId) {
            $userCourseSlugs->push(Course::find($userCourseId)->pluck('slug')->first());
        }

        $filterd = $pushNotifications->filter(function ($pushNotification) use ($userCourseSlugs) {
            if ($pushNotification->cta_type == 'Internal' && $pushNotification->internal_cta_type == "Course") {
                return ! $userCourseSlugs->contains("$pushNotification->internal_course_slug");
            }

            return true;
        });

        return $this->excludeUserNews($filterd->flatten());
    }

    public function excludeUserNews($pushNotifications)
    {
        $userNewsIds = user()->readNews();
        $userNewsSlugs = collect();

        foreach ($userNewsIds as $userNewsId) {
            if ($news = News::find($userNewsId)) {
                $userNewsSlugs->push($news->pluck('slug')->first());
            }
        }

        $filtered = $pushNotifications->filter(function ($pushNotification) use ($userNewsSlugs) {
            if ($pushNotification->cta_type == 'Internal' && $pushNotification->internal_cta_type == "News") {
                return ! $userNewsSlugs->contains("$pushNotification->internal_news_slug");
            }

            return true;
        });

        return $this->pushNotifications = $filtered->flatten();
    }

    /**
     * @return JsonResponse
     */
    public function markPushNotificationRead()
    {

        if (session()->has('pushViewed')) {
            session()->push('pushViewed', request('pushNotificationId'));
        }
        else {
            session()->put('pushViewed', [request('pushNotificationId')]);
        }
        if (user()) {
            PushNotifications::find(request('pushNotificationId'))->user()->save(User::find(user()->id));

            return response()->json('success');
        }

        return response()->json('success');
    }
}
