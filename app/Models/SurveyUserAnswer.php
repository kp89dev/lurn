<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SurveyUserAnswer
 * @package App\Models
 *
 * @property Survey survey
 * @property SurveyQuestion question
 * @property User user
 */
class SurveyUserAnswer extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the survey this entry belongs to.
     *
     * @return BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    /**
     * Get the question this entry belongs to.
     *
     * @return BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    /**
     * Get the user this entry belongs to.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
