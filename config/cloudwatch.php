<?php
return [
    'region'      => 'us-east-1',
    'version'     => 'latest',
    'credentials' => [
        'key' => env('CLOUDWATCH_KEY', ''),
        'secret' => env('CLOUDWATCH_SECRET')
    ]
];
