<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lesson;
use Tests\Admin\User\UserTest;

class Test extends Model
{
    protected $guarded = ['id'];
    protected $dates = ['created_at', 'updated_at'];
    
    public function questions()
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(TestQuestionAnswer::class, TestQuestion::class, null, 'question_id');
    }
    
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function results()
    {
        return $this->hasMany(TestResult::class);
    }
    
    public function afterLesson()
    {
        return $this->hasOne(Lesson::class, 'id', 'after_lesson_id');
    }
    
    public function getModule()
    {
        return $this->afterLesson->module;
    }

    public function getModuleAttribute()
    {
        return $this->getModule();
    }
    
    public function getRelated()
    {
        return (object) [
            'next'     => $this->getNext(),
            'previous' => $this->afterLesson,
        ];
    }
    
    /**
     * Returns the next lesson available, if any.
     *
     * @return mixed
     */
    public function getNext()
    {    
        $module = $this->getModule();

        $next = $module
            ->lessons()
            ->enabled()
            ->where('order', '>', $this->afterLesson->order)
            ->orderBy('order')
            ->first();
    
        if (! $next && $next = $module->getNext()) {
            $next = $next->lessons()->enabled()->orderBy('order')->first();
        }
    
        return $next;
    }
    
    /**
     * Returns the previous lesson available, if any.
     *
     * @return mixed
     */
    public function getPrevious()
    {
        $module = $this->getModule();
        
        $previous = $module->lessons()
        ->enabled()
        ->where('order', '<', $this->order)
        ->orderBy('order', 'desc')
        ->first();
    
        if (! $previous && $previous = $module->getPrevious()) {
            $previous = $module->lessons()->enabled()->orderBy('order', 'desc')->first();
        }
    
        return $previous;
    }

    public function getOrderedQuestions()
    {
        return $this->questions()->enabled()->orderby('order')->get();
    }
    
    public function scopeEnabled($query)
    {
        $query->where($this->getTable().'.status', 1);
    }

    /**
     * Check the answers provided by the users against the DB
     *
     * @param array $userAnswers
     * @return array
     */
    public function checkAnswers(array $userAnswers)
    {
        $correct = [];
        $incorrect = [];
        $questions = $this->getOrderedQuestions();
        $storableAnswers = [];

        foreach ($questions as $question) {
            if (! isset($userAnswers[$question->id])) {
                $incorrect[] = $question->id;
                continue;
            }

            $userAnswer = $userAnswers[$question->id];
            $userAnswer = is_array($userAnswer) ? $userAnswer : [$userAnswer];

            $correctAnswers  = array_flatten($question->answers()->whereIsAnswer(1)->get(['id'])->toArray());
            $isCorrect = true;

            foreach($userAnswer as $ans) {
                $ansId = is_array($ans) ? current($ans) : $ans;
                if (in_array($ansId, $correctAnswers, false)) {
                    $storableAnswers[$question->id] = [$ansId => 'correct'];
                    continue;
                }

                $storableAnswers[$question->id] = [$ansId => 'incorrect'];
                $isCorrect = false;
            }

            $isCorrect
                ? $correct[] = $question->id
                : $incorrect[] = $question->id;
        }

        $mark = round(count($correct) / count($questions) * 100, 2);

        $this->storeResult($mark, $storableAnswers);

        return compact('mark', 'correct', 'incorrect');
    }

    private function storeResult($mark, $storableAnswers)
    {
        if ($result = TestResult::whereUserId(user()->id)->whereTestId($this->id)->first()) {
            $result->no_of_tries += 1;
        } else {
            $result = new TestResult;
            $result->no_of_tries = 1;
            $result->test_id = $this->id;
            $result->user_id = user()->id;
        }

        $result->answer = $storableAnswers;
        $result->mark = $mark;
        $result->save();
    }
    
    /**
     * Check if a user has previously passed this test
     * 
     * @param User $user
     * @return boolean
     */
    public function userHasPassed($user) {
        $results = TestResult::where('user_id', '=', $user->id)
            ->where('test_id', '=', $this->id)
            ->where('mark', '>=', 75)
            ->get();
        
        return (bool) $results->count();
    }
    
    public function getSrc($i = '')
    {
        switch ($i) {
            case 'custom_completion_background':
                return str_finish(config('app.cdn_static'), '/') . $this->custom_completion_background;
            case 'custom_completion_header':
                return str_finish(config('app.cdn_static'), '/') . $this->custom_completion_header;
            case 'custom_completion_badge':
                return str_finish(config('app.cdn_static'), '/') . $this->custom_completion_badge;
        }
    }
}
