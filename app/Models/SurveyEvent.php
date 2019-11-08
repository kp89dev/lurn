<?php
/**
 * Date: 3/22/18
 * Time: 10:54 AM
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class SurveyEvent
 * @package App\Models
 *
 * @property int id
 * @property string name
 * @property string trigger
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Collection surveys
 */
class SurveyEvent extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsToMany
     */
    public function surveys()
    {
        return $this->belongsToMany(Survey::class);
    }
}
