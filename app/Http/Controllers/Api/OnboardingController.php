<?php

namespace App\Http\Controllers\Api;

use App\Events\Onboarding\EvaluationCompleted;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Survey;
use App\Models\SurveyUserAnswer;
use Illuminate\Http\Request;
use App\Models\Onboarding\Mission;
use App\Models\Onboarding\ReferralScenario;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class OnboardingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function markScenarioComplete(Request $id)
    {
        $scenario_id = $id->scenario_id;

        $user = user();

        $onboarding = new Mission($user);

        $complete = $onboarding->scenarios[$scenario_id]->isCompleted($user);

        if (! $complete) {
            $complete = $onboarding->scenarios[$scenario_id]->complete($user);
        }

        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReferralEmail(Request $request)
    {
        $emails = explode(',', preg_replace('#\s+#', ',', trim($request->referrals)));
        $scenario = new ReferralScenario(['id' => 5]);
        $goodEmails = [];

        foreach ($emails as $email) {
            if (count($goodEmails) < 3 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $goodEmails[] = $email;
                $scenario->complete(user(), $email);
                /**
                 * TODO Mail out referral links
                 */
            }
        }

        if ($scenario->isCompleted(user())) {
            return response()->json(['success' => true, 'pts' => count($goodEmails) * $scenario->points]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfilePicture(Request $request)
    {
        if ($request->file('file')) {
            $path = $request->file('file')->store('user', 'static');

            if ($path) {
                $this->deleteOldImage($request->user());

                $resized = Image::make(Storage::disk('static')->get($path))->fit(256, 256)->stream();
                Storage::disk('static')->put($path, $resized);

                tap($request->user()->setting, function ($instance) use ($path) {
                    $instance->image = $path;
                })->save();

                $this->completeScenario(1);

                return response()->json([
                    'success' => true,
                    'image'   => user()->getPrintableImageUrl(),
                ]);
            }
        }

        return response()->json(['success' => false]);
    }

    /**
     * @param $user
     */
    private function deleteOldImage($user)
    {
        if ($user->setting->image) {
            Storage::disk('static')->delete($user->setting->image);
        }
    }

    private function completeScenario($id)
    {
        $scenario_id = $id;

        $user = user();

        $onboarding = new Mission($user);

        $complete = $onboarding->scenarios[$scenario_id]->isCompleted($user);

        if (! $complete) {
            $onboarding->scenarios[$scenario_id]->complete($user);
        }

        return true;
    }

    public function saveSurvey(Survey $survey)
    {
        if (is_array($answers = request()->answers)) {
            // Remove old user answers.
            SurveyUserAnswer::whereUserId(user()->id)
                ->whereSurveyId($survey->id)
                ->delete();

            $questionIds = $survey->questions()->whereIn('id', array_keys($answers))->pluck('id');
            $answerIds = $survey->questionAnswers()->whereIn('question_id', $questionIds)->pluck('id');

            foreach ($answers as $qId => $questionAnswers) {
                if (! $questionIds->contains($qId)) {
                    continue;
                }

                foreach ($questionAnswers as $answerId) {
                    if (! $answerIds->contains($answerId)) {
                        continue;
                    }

                    SurveyUserAnswer::create([
                        'user_id'     => user()->id,
                        'survey_id'   => $survey->id,
                        'question_id' => $qId,
                        'answer_id'   => $answerId,
                    ]);
                }
            }

            event(new EvaluationCompleted(user()));
        }
    }

    /**
     * Enrolls the authenticated user into the given course, if it's free.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function firstCourse(Request $request)
    {
        $id = request('id');

        $this->validate(
            $request,
            ['id' => 'required|exists:courses'],
            ['id.*' => 'Please select a course.']
        );

        $course = Course::find($id) or abort(404);

        if ($course->free) {
            user()->enroll($course);

            return response()->json(['courseUrl' => $course->url]);
        }
    }
}

