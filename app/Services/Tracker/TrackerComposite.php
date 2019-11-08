<?php
namespace App\Services\Tracker;

use App\Services\Contracts\TrackerInterface;

class TrackerComposite implements TrackerInterface
{
    /**
     * @type
     */
    private $trackers;

    public function __construct($trackers)
    {
        $this->trackers = $trackers;
    }

    public function track($eventName, $args, $push)
    {
        foreach ($this->trackers as $tracker) {
            $tracker->track($eventName, $args, $push);
        }
    }

    public function identity($identity)
    {
        foreach ($this->trackers as $tracker) {
            $tracker->identity($identity);
        }
    }
}
