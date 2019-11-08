<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class FacebookShare extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.postId' => 'required'
        ];

        $this->description = 'Shared on Facebook';

        $this->points = 100;
    }

    public function handle($data)
    {
        return $this->transform($data);
    }
}