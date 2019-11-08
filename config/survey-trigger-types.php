<?php

return [
    [
        'key' => 'timed',
        'display_name' => 'Timed',
        'description' => 'A minimum time interval is specified indicating how often a Question from the Survey can' .
            ' be presented.',
    ],
    [
        'key' => 'event',
        'display_name' => 'Event',
        'description' => 'When a specific event occurs, trigger the presentation of the next Question from a Survey.',
    ],
    [
        'key' => 'combination',
        'display_name' => 'Combination',
        'description' => 'A combination of Timed and Event whereby the minimum time since the last Question must' .
            ' elapse before an Event can trigger a Survey.',
    ],
    [
        'key' => 'standalone',
        'display_name' => 'Standalone',
        'description' => 'The Assessment is available for access by users but is not “presented”, the user just opens' .
            ' it up.  There would be no “pop-ups” presented.'
    ],
];
