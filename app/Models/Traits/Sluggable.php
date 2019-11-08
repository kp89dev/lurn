<?php

namespace App\Models\Traits;

/**
 * @property $attributes
 */
trait Sluggable
{
    /**
     * Creates a slug from the title column of the resource.
     *
     * @param $title
     */
    public function setTitleAttribute($title)
    {
        $slug = str_slug($title);
        $check = self::where('slug', 'like', "$slug%")
            ->where('id', '!=', isset_or($this->attributes, 'id', 0))
            ->orderBy('slug', 'desc');

        // Assume the slug's uniqueness relative to the parent if this is a module or a lesson.
        foreach (['course_id', 'module_id'] as $column) {
            if ($id = isset_or($this->attributes, $column, false)) {
                $check->where([$column => $id]);
            }
        }

        // If the slug is in use.
        if ($resource = $check->first()) {
            // Find the next available slug.
            $index = min(2, (int) str_end($resource->slug, '-') + 1);
            $slug = "$slug-$index";
        }

        $this->attributes['title'] = $title;
        $this->attributes['slug'] = $slug;
    }
}
