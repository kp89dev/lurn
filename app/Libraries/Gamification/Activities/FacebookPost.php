<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FacebookPost extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.postId' => 'required'
        ];

        $this->description = 'Posted on Facebook';

        $this->points = 50;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}