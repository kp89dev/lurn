<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CourseInfusionsoft extends Model
{
    protected $table = 'course_infusionsoft';
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get next infusionsoft merchant ID
     *
     * @return mixed
     */
    public function getNextMerchantId()
    {
        $merchantId = Cache::tags(['is_merchant'])->get('is_merchant:next:' . $this->is_account);

        if (! is_null($merchantId)) {
           return $merchantId;
        }

        $this->rotateMerchantId();
        return Cache::tags(['is_merchant'])->get('is_merchant:next:' . $this->is_account);
    }

    /**
     * @return null
     */
    public function rotateMerchantId()
    {
        $availableIds = Cache::tags(['is_merchant'])->get('is_merchant:available:' . $this->is_account);

        if (!$availableIds) {
            $dbRecord = (new InfusionsoftMerchantId)->where(['account' => $this->is_account])->firstOrFail();

            if (! count($dbRecord->ids)) {
                throw new \RuntimeException('No id defined for ' . $this->is_account . ' infusionsoft account');
            }

            $availableIds = json_encode($dbRecord->ids);
            Cache::tags(['is_merchant'])->forever('is_merchant:available:' . $this->is_account, $availableIds);
        }

        $availableIds = json_decode($availableIds);
        $currentId    = array_search(
            Cache::tags(['is_merchant'])->get('is_merchant:next:' . $this->is_account, $availableIds[0]),
            $availableIds,
            false
        );
        $nextId       = 0;

        if (array_key_exists($currentId +1, $availableIds)) {
            $nextId = $currentId+1;
        }

        Cache::tags(['is_merchant'])->forever('is_merchant:next:' . $this->is_account, $availableIds[$nextId]);
    }
}
