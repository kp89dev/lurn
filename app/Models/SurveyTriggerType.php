<?php
/**
 * Date: 3/20/18
 * Time: 11:37 AM
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class SurveyTriggerType
 * @package App\Models
 *
 * @property int id
 * @property string key
 * @property string display_name
 * @property string description
 *
 * @property Collection surveys
 */
class SurveyTriggerType extends Model
{
    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}
