<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class InstagramPost extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.postId' => 'required'
        ];

        $this->description = 'Posted on Instagram';

        $this->points = 50;
    }
    
    public function handle($data)
    {
        return $this->transform($data);
    }
}