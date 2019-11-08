<?php
namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\CourseVanillaForum;
use App\Services\VanillaJsConnect\VanillaJsConnect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VanillaForumController extends Controller
{

    public function prepRequest($courseId)
    {
        $vanillaForum = CourseVanillaForum::where('course_id', $courseId)->first();
        $user = Auth::user();

        if ($user && $user instanceof User) {
            $userForumStatus = UserCourse::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->where('status', '!=', 1)
                ->pluck('forum_rules')
                ->first();

            $response = array(
                'showRules' => FALSE,
                'forum_link' => $vanillaForum->url,
            );
            
            session(['forum_u' => $user->id]);
            session(['forum_c' => $courseId]);

            if ($vanillaForum->forum_rules) {
                if (!$userForumStatus || $userForumStatus == 0) {
                    $response = array(
                        'showRules' => TRUE,
                    );
                }
            }

            return response()->json($response);
        }
        $response = array(
            'showRules' => FALSE
        );

        return response()->json($response);
    }

    public function userRules(Request $request)
    {
        $userCourse = UserCourse::where('user_id', $request->userId)
            ->where('course_id', $request->courseId)
            ->where('status', '!=', 1)
            ->first();
        $userCourse->forum_rules = $request->status;
        $userCourse->save();
    }

    public function sso(VanillaJsConnect $vanillaJsConnect)
    {
        if (request('callback') == 'test'){
            $vanillaForum = CourseVanillaForum::where('client_id', request('client_id'))->first();
            $forumCourse = $vanillaForum->course_id;
        } else {
            $forumCourse = session('forum_c');
            $vanillaForum = CourseVanillaForum::where('course_id', $forumCourse)->first(); 
        }
        
        $user = Auth::user();
        $userResponse = [];

        if ($user && $user instanceof User) {
            $userForumStatus = UserCourse::where('user_id', $user->id)
                ->where('course_id', $forumCourse)
                ->where('status', '!=', 1)
                ->pluck('forum_rules')
                ->first();

            if ($vanillaForum->forum_rules) {
                if (!$userForumStatus || $userForumStatus == 0) {

                    return redirect()->route('course.forum', $forumCourse);
                }
            }

            $userResponse['uniqueid'] = $user->id;
            $userResponse['name'] = $user->name;
            $userResponse['email'] = $user->email;
            $userResponse['ip'] = request()->getClientIp();
            
            return $vanillaJsConnect->writeJsConnect($userResponse, $vanillaForum->client_id, $vanillaForum->client_secret, true);
        }

        return redirect()->route('login');
    }
}
