<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class YoutubeVideo extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.youtube_link' => 'required'
        ];

        $this->description = 'Posted on YouTube';

        $this->points = 150;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}