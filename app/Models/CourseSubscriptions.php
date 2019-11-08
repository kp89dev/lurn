<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSubscriptions extends Model
{
    protected $table = 'user_courses';
    protected $guarded = ['id'];

    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
        'refunded_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subscription_payment' => 'boolean',
    ];

    public function scopeActive($query)
    {
        $query->whereNull('cancelled_at');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function infusionsoft()
    {
        return $this->belongsTo(CourseInfusionsoft::class, 'course_infusionsoft_id');
    }

    public function getStatusNameAttribute()
    {
        return Course::$userStatuses[$this->status ?? 0];
    }

    /**
     * A course subscription expires when:
     * - It's a subscription based payment type,
     * - The last payment is not null,
     * - The last payment has been made less more than 60 days ago,
     * - And if the course requires unlimited monthly payments
     *   OR if all the required number of payments has not been reached yet.
     *
     * @return bool
     */
    public function getExpiredAttribute()
    {
        return $this->subscription_payment
            && ! is_null($this->paid_at)
            && $this->paid_at->lessThan(now()->subDays(60))
            && (is_null($this->payments_required) || $this->payments_made < $this->payments_required);
    }
}
