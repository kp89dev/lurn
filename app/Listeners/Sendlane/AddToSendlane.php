<?php
namespace App\Listeners\Sendlane;

use App\Events\User\UserEnrolled;
use App\Services\Sendlane\Sendlane as SendlaneService;

class AddToSendlane
{
    /**
     * @param UserEnrolled $event
     */
    public function handle(UserEnrolled $event)
    {
        if (app()->environment('local')) {
            return true;
        }

        $courseSendlane = $event->course->sendlane;
        if($courseSendlane->sendlaneCredentials) {
            $accountDetails = $courseSendlane->sendlaneCredentials;
            $apiData        = $accountDetails->prepareCredentialsForRequest();
    
            $client = app(SendlaneService::class, $apiData);
            $client->subscribers->add(
                sprintf('%s<%s>', $event->user->name, $event->user->email),
                (int) $courseSendlane->list_id
            );
        }
    }
}
