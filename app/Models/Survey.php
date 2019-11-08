<?php

namespace App\Models;

use App\Models\Queries\Traits\SurveyScopes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Survey
 * @package App\Models
 *
 * @property int id
 * @property string title
 * @property string description
 * @property int enabled
 * @property int require_login
 * @property Carbon start_date
 * @property Carbon end_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property int survey_type_id
 * @property int survey_trigger_type_id
 * @property int survey_question_ordering_id
 * @property SurveyType surveyType
 * @property SurveyTriggerType surveyTriggerType
 * @property SurveyQuestionOrdering surveyQuestionOrdering
 * @property string typeName
 * @property string displayTypeName
 * @property string triggerTypeName
 * @property string displayTriggerTypeName
 * @property string questionOrderingName
 * @property string displayQuestionOrderingName
 * @property Collection questions
 * @property Collection answers
 *
 * Scopes:
 * @method static byEvent(string $event)
 * @method static unanswered()
 */
class Survey extends Model
{
    use SurveyScopes;

    /**
     * @var array
     */
    public static $types = [
        'survey'     => 'Timed Survey',
        'onboarding' => 'Onboarding Survey',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'start_date',
        'end_date',
    ];

    /**
     * @return BelongsTo
     */
    public function surveyType()
    {
        return $this->belongsTo(SurveyType::class);
    }

    /**
     * @return BelongsTo
     */
    public function surveyTriggerType()
    {
        return $this->belongsTo(SurveyTriggerType::class);
    }

    /**
     * @return BelongsTo
     */
    public function surveyQuestionOrdering()
    {
        return $this->belongsTo(SurveyQuestionOrdering::class);
    }

    /**
     * @return BelongsToMany
     */
    public function surveyCustomCodes()
    {
        return $this->belongsToMany(SurveyCustomCode::class);
    }

    /**
     * @return BelongsToMany
     */
    public function surveyEvents()
    {
        return $this->belongsToMany(SurveyEvent::class);
    }

    /**
     * Get the questions belonging to this survey.
     */
    public function questions()
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function questionAnswers()
    {
        return $this->hasMany(SurveyAnswer::class);
    }

    /**
     * Get the user answers belonging to this survey.
     */
    public function answers()
    {
        return $this->hasMany(SurveyUserAnswer::class);
    }

    /**
     * Get the results belonging to this survey.
     */
    public function results()
    {
        return $this->hasMany(SurveyView::class);
    }

    /**
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return $this->surveyType->key;
    }

    /**
     * @return string
     */
    public function getDisplayTypeNameAttribute()
    {
        return $this->surveyType->display_name;
    }

    /**
     * @return string
     */
    public function getQuestionOrderingNameAttribute()
    {
        return $this->surveyQuestionOrdering->key;
    }

    /**
     * @return string
     */
    public function getDisplayQuestionOrderingNameAttribute()
    {
        return $this->surveyQuestionOrdering->display_name;
    }

    public static function getOnboardingSurvey()
    {
        return self::with([
                'questions.answers',
                'questions' => function ($query) {
                    $query->whereEnabled(1);
                },
            ])
            ->whereHas('surveyType', function ($query) {
                $query->where('key', 'onboarding');
            })
            ->where('surveys.enabled', 1)
            ->first();
    }
}
