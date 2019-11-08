<?php

namespace App\Models\Traits;

trait FindBySlug
{
    /**
     * Searches the model by the slug column.
     *
     * @param      $slug
     * @param bool $enabled
     * @return mixed
     */

    public static function findBySlug($slug, $enabled = true)
    {
        $data = self::whereSlug($slug);
        $enabled && $data->enabled();

        return $data->first();
    }

    public function scopeFindBySlug($query, $slug, $enabled = true)
    {
        $query->whereSlug($slug);
        $enabled && $query->enabled();

        return $query->first();
    }
}