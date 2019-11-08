<?php
namespace App\Models\Tracker;

use Illuminate\Database\Eloquent\Model;

class Identity extends Model
{
    protected $table = 'tr_identities';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getCampaignVisits()
    {
        return $this->hasMany(Visit::class, 'visitor_id', 'visitor_id')
            ->whereNotNull('campaign_id');
    }

}
