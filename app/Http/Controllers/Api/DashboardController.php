<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param $key
     * @return JsonResponse
     */
    public function hideMessage($key)
    {
        return response()->json([
            'success' => user()->setting
                ->setMessageState("show-$key", false)
                ->save()
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function unreadNews()
    {
        return response()->json(
            user()->getUnreadNews()->take(5)->get()
        );
    }

    /**
     * @return JsonResponse
     */
    public function markNewsRead()
    {
        if (! is_array($ids = request('ids'))) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => user()->readNews()->attach($ids)
        ]);
    }

    public function feedback()
    {
        $data = request()->only('grade', 'feedback');
        Feedback::create($data + ['user_id' => user() ? user()->id : null]);

        user()->setting
            ->setMessageState('show-recommendation-question', false)
            ->save();
    }
}
