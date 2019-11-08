<?php
namespace App\Listeners\Tracking;

use App\Events\User\UserEnrolled;
use App\Services\Contracts\TrackerInterface;

class TrackEnrollments
{
    public function handle(UserEnrolled $event)
    {
        $tracker = app()->make(TrackerInterface::class);

        $tracker->track('Enrolled', [
            'course'    => $event->course->title,
            'course_id' => $event->course->id,
            'upsell'    => $event->course->infusionsoft->upsell
        ], true);

        $tracker->track('payment', [
            'amount'  => $event->course->infusionsoft->price,
            'product' => $event->course->infusionsoft->is_product_id,
            'upsell'  => $event->course->infusionsoft->upsell
        ], true);
    }
}
