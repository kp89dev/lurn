<?php

namespace App\Models\Traits;

/**
 * @method static search()
 */
trait Searchable
{
    public function scopeSearch($query)
    {
        if ($terms = request('q')) {
            $formattedTerms = preg_replace('/[^a-z0-9 ]/i', '', trim($terms));
            $formattedTerms = '%' . preg_replace('/\s+/i', '%', $formattedTerms) . '%';

            $query->where(function ($query) use ($formattedTerms) {
                $query->where('title', 'like', $formattedTerms)
                    ->orWhere('description', 'like', $formattedTerms);

                if ($this->getTable() == 'seo_courses') {
                    $query->orWhere('keywords', 'like', $formattedTerms);
                }
            });
        }
    }
}
