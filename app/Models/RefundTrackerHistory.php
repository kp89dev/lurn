<?php
/**
 * Date: 3/20/18
 * Time: 1:18 PM
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class RefundTrackerHistory
 * @package App\Models
 *
 * @property int id
 * @property int refund_tracker_id
 * @property RefundTracker refundTracker
 * @property string activity
 * @property string|null error_message
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class RefundTrackerHistory extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function refundTracker()
    {
        return $this->belongsTo(RefundTracker::class);
    }
}