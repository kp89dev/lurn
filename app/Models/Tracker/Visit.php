<?php
namespace App\Models\Tracker;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'tr_visits';
    protected $guarded = ['id'];

    public function campaign()
    {
        return $this->hasOne(Campaign::class, 'id', 'campaign_id');
    }
}
