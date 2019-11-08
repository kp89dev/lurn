<?php
namespace App\Models;

use App\Models\QueryBuilder\CourseResources;
use App\Models\Traits\Sluggable;
use App\Models\Traits\FindBySlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lesson extends Model
{
    use Sluggable, FindBySlug;

    protected $guarded = ['id'];

    public function scopeEnabled($query)
    {
        $query->where($this->getTable() . '.status', 1);
    }

    public function scopeOrdered($query, $dir = 'asc')
    {
        $query->orderBy('order', $dir);
    }

    public function scopeCounted($query)
    {
        $query->where($this->getTable() . '.status', 1)
            ->where('modules.status', 1);
    }

    public function scopeAlphabetical($query, $dir = 'asc')
    {
        return $query->orderBy('title', $dir);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(LessonSubscriptions::class);
    }

    public function test()
    {
        return $this->hasOne(Test::class, 'after_lesson_id');
    }

    public static function getNextOrderValueForModule($module)
    {
        $order = DB::table('lessons')
            ->select('order')
            ->whereModuleId($module)
            ->orderBy('order', 'DESC')
            ->limit(1)
            ->get();

        if ($order->count()) {
            return (int) $order[0]->order + 1;
        }

        return 1;
    }

    /**
     * Returns the index of the current question relative to the module it belongs to.
     *
     * @return int
     * @throws \Exception
     */
    public function getIndex()
    {
        $course = request('course') instanceof Course ? request('course') : $this->module->course;

        $lessons = (new CourseResources($course))
            ->modulesWithCountersAndProgress($this->module_id)
            ->addLessons()
            ->get()
            ->first();

        $lessons = $lessons ? $lessons->orderedLessons : [];

        foreach ($lessons as $i => $lesson) {
            if ($lesson->slug == $this->slug) {
                return $i + 1;
            }
        }

        return 0;
    }

    /**
     * Returns the next lesson or test available, if any.
     *
     * @param bool $ignoreLinks
     * @return mixed
     */
    public function getNext($ignoreLinks = false)
    {
        if ($this->test && $this->test->status == 1) {
            return $this->test;
        }

        $next = DB::table('lessons')
            ->select('lessons.*')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->join('courses', 'courses.id', '=', 'modules.course_id')
            ->where('courses.id', $this->module->course->id)
            ->where('modules.status', 1)
            ->where('lessons.status', 1)
            ->where('modules.order', '>=', $this->module->order)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lessons.order', '>', $this->order)
                        ->where('modules.id', $this->module_id);
                })->orWhere('modules.id', '!=', $this->module_id);
            });

        if ($ignoreLinks) {
            $next->where('lessons.type', 'Lesson')
                 ->where('modules.type', 'Module');
        }

        $next->orderBy('modules.order')
             ->orderBy('lessons.order');

        $next = $next->first();
        $next = $next
            ? self::hydrate([$next])->first()
            : user()->getFirstIncompleteLesson($this->module->course, $this);

        return $next;
    }

    /**
     * Returns the previous lesson available, if any.
     *
     * @param bool $ignoreLinks
     * @return mixed
     */
    public function getPrevious($ignoreLinks = false)
    {
        $previous = DB::table('lessons')
            ->select('lessons.*')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->join('courses', 'courses.id', '=', 'modules.course_id')
            ->where('courses.id', $this->module->course->id)
            ->where('modules.status', 1)
            ->where('lessons.status', 1)
            ->where('modules.order', '<=', $this->module->order)
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('lessons.order', '<', $this->order)
                        ->where('modules.id', $this->module_id);
                })->orWhere('modules.id', '!=', $this->module_id);
            });

        if ($ignoreLinks) {
            $previous->where('lessons.type', 'Lesson')
                ->where('modules.type', 'Module');
        }

        $previous->orderBy('modules.order', 'desc')
            ->orderBy('lessons.order', 'desc');

        $previous = $previous->first();
        $previous = $previous ? self::hydrate([$previous])->first() : null;

        return $previous ? ($previous->test ?: $previous) : null;
    }

    public function getRelated()
    {
        return (object) [
            'next'     => $this->getNext(true),
            'previous' => $this->getPrevious(true),
        ];
    }

    public function getNotes()
    {
        return LessonUserNote::whereUserId(user()->id)
                ->whereLessonId($this->id)
                ->first();
    }

    public function getNotesAttribute()
    {
        $userNotes = $this->getNotes();

        return $userNotes ? $userNotes->notes : null;
    }

    public function getUnsafeNotesAttribute()
    {
        $userNotes = $this->getNotes();

        return $userNotes ? $userNotes->unsafeNotes : null;
    }

    public function isLocked()
    {
        return $this->module->isLocked();
    }

    public function getUrl()
    {
        return route('lesson', [$this->module->course->slug, $this->module->slug, $this->slug]);
    }
}
