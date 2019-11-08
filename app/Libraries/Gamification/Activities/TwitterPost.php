<?php

namespace Gamification\Activities;

use Gamification\Transformers\Activity;

class TwitterPost extends Activity
{
    public function __construct()
    {
        $this->schema = [
            'details.postId' => 'required'
        ];

        $this->description = 'Posted on Twitter';

        $this->points = 50;
    }
    
    public function handle($data)
    {
        return $this->transform($data);
    }
}