<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class SurveyQuestion
 * @package App\Models
 *
 * @property Collection userAnswers
 */
class SurveyQuestion extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the survey this entry belongs to.
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    /**
     * Get the answers belonging to this survey.
     */
    public function answers()
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function userAnswers()
    {
        return $this->hasMany(SurveyUserAnswer::class);
    }
}
