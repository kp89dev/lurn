<?php
/**
 * Date: 3/20/18
 * Time: 1:38 PM
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class RefundTrackerHistory
 * @package App\Models
 *
 * @property int id
 * @property string identifier
 * @property Carbon|null started_at
 * @property Carbon|null finished_at
 * @property int failed
 * @property string|null failed_reason
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Collection histories
 */
class RefundTracker extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'started_at',
        'finished_at',
    ];

    /**
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(RefundTrackerHistory::class);
    }
}
