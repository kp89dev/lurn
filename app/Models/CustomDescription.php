<?php
/**
 * Created by PhpStorm.
 * User: rjacobsen
 * Date: 3/5/18
 * Time: 7:07 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomDescription extends Model
{
    protected $fillable = [
        'description_type_id',
        'description',
    ];

    /**
     * @param $value
     * @return int
     */
    public function getDescriptionTypeIdAttribute($value)
    {
        return (int)$value;
    }

    /**
     * @return BelongsTo
     */
    public function descriptionType()
    {
        return $this->belongsTo(DescriptionType::class);
    }

    /**
     * @return BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_custom_description')->withTimestamps();
    }

    public function scopePostRegistration($query)
    {
        return $query->whereHas('descriptionType', function ($query) {
            $query->whereName('post-registration');
        });
    }
}