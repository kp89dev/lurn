<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailStatus extends Model
{
    protected $guarded = ['id'];
    protected $dates = [
        'created_at',
        'updated_at',
        'last_timestamp',
    ];

    protected $statuses = [
        0   => 'sent',
        50  => 'delivery',
        100 => 'open',
        200 => 'click',
        25  => 'bounce',
    ];

    public function getStatusAttribute($value)
    {
        return ucfirst($this->statuses[$value]);
    }

    public function setStatusAttribute($value)
    {
        if (ctype_digit($value)) {
            $this->attributes['status'] = $value;
        } elseif (($statusId = array_search(strtolower($value), $this->statuses)) !== false) {
            $this->attributes['status'] = $statusId;
        }
    }

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function getStatusColorAttribute()
    {
        switch ($this->original['status']) {
            case 25:
                return 'danger';
            case 50:
                return 'info';
            case 100:
            case 200:
                return 'success';
        }

        return 'info';
    }
}
