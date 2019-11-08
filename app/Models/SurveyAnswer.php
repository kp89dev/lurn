<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class SurveyAnswer
 * @package App\Models
 *
 * @property Survey survey
 * @property SurveyQuestion question
 * @property SurveyUserAnswer results
 */
class SurveyAnswer extends Model
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
     * Get the results for this answer.
     *
     * @return HasMany
     */
    public function results()
    {
        return $this->hasMany(SurveyUserAnswer::class, 'answer_id', 'id');
    }
}
