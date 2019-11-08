<?php

namespace App\Http\Controllers\Remote;

use App\Events\User\UserCreatedThroughInfusionsoft;
use App\Events\User\UserEnrolled;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends AbstractRemoteController
{
    protected $expectedFields = [
        'contact_id',
        'product_id',
        'invoice_id',
        'name',
        'email',
    ];

    public function index(Request $request)
    {
        $courseIS = CourseInfusionsoft::where('is_product_id', $request->product_id)
            ->orWhere('is_subscription_product_id', $request->product_id)
            ->first();

        if (is_null($courseIS)) {
            $this->logger->info('Infusionsoft Request Failed - Course not found', $request->all());

            return response('', 204);
        }

        $course = $courseIS->course;
        $user = $this->getUser();
        $hasAccess = $user->courses()->whereCourseId($course->id)->first();

        if ($hasAccess instanceof Course) {
            $this->logger->info('Infusionsoft Request Failed - User has access', $request->all());

            return response('', 204);
        }

        $user->courses()->attach($course, $request->only('invoice_id'));

        event(new UserEnrolled($user, $course));

        return response('', 204);
    }

    private function getUser()
    {
        $user = User::whereEmail(request('email'))->first();

        if (! is_null($user)) {
            if ($user->mergedIntoAccount()->first()) {
                $user = $user->mergedIntoAccount()->first();
            }
        }

        return $user ?? $this->importAndGetUser();
    }

    private function importAndGetUser()
    {
        $importedUser = ImportedUser::where('email', request('email'))->first();

        if (! is_null($importedUser) && ($user = $importedUser->mergedInto()->first())) {
            return $user;
        }

        return $this->registerAndGetUser();
    }

    private function registerAndGetUser()
    {
        $user = User::create([
            'name'     => request('name'),
            'email'    => request('email'),
            'status'   => 'confirmed',
            'password' => bcrypt(Str::random()),
        ]);

        event(new UserCreatedThroughInfusionsoft($user));

        return $user;
    }
}
