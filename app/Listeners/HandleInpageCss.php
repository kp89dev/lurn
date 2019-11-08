<?php

namespace App\Listeners;

use App\Models\Course;
use App\Models\CustomDescription;
use Less_Parser;

class HandleInpageCss
{
    protected $handlers = [
        Course::class => [
            'description' => '#course-description',
            'snippet'     => '.course-snippet',
        ],

        CustomDescription::class => [
            'description' => '#course-description',
        ],
    ];

    /**
     * Handle the event for the defined $handlers.
     *
     * @param $event
     */
    public function handle($event)
    {
        if (isset($this->handlers[get_class($event)])) {
            $isolators = $this->handlers[get_class($event)];

            // Check if the fields in need of isolation are dirty.
            if ($event->isDirty(array_keys($isolators))) {
                $this->isolateCss($event, $isolators);
            }
        }
    }

    /**
     * Searches for <style>...</style> blocks and isolate them
     * to the selectors defined on $isolators configuration.
     *
     * @param $instance
     * @param $isolators
     */
    protected function isolateCss($instance, $isolators)
    {
        foreach ($isolators as $field => $isolator) {
            $styleRegex = '#<style[^>]*?>(.*?)</style>#si';

            // Remove the isolator first, to make sure that we don't apply it in a loop.
            $instance->$field = str_replace($isolator, '', $instance->$field);

            // Isolate the CSS.
            try {
                $instance->$field = preg_replace_callback($styleRegex, function ($matches) use ($isolator) {
                    $lessCompiler = new Less_Parser;
                    $less = sprintf('%s { %s }', $isolator, $matches[1]);
                    $css = $lessCompiler->parse($less)->getCss();

                    return sprintf('<style>%s</style>', $css);
                }, $instance->$field);
            } catch (\Exception $e) {}
        }
    }
}
