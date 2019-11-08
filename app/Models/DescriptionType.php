<?php
/**
 * Created by PhpStorm.
 * User: rjacobsen
 * Date: 3/5/18
 * Time: 7:07 PM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DescriptionType extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany
     */
    public function customDescriptions()
    {
        return $this->hasMany(CustomDescription::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePostRegistration($query)
    {
        return $query->whereName('post-registration');
    }
}