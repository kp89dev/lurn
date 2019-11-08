<?php
namespace App\Models;

use App\Models\QueryBuilder\CourseResources;
use App\Models\Traits\Sluggable;
use App\Models\Traits\FindBySlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Module extends Model
{

    use Sluggable,
        FindBySlug;

    protected $guarded = ['id'];

    public function scopeEnabled($query)
    {
        $query->whereStatus(1);
    }

    public function scopeOrdered($query)
    {
        $query->orderBy('order');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    /**
     * Returns a list of tests assignable to a specific locked module
     *
     * This method allows the listing of tests inside a specific course
     * that can be used to lock a module. It automatically filters out
     * any test inside that module, or outside that module's course.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function availableTest()
    {
        return Test::select('tests.*')
                ->join('lessons', 'tests.after_lesson_id', '=', 'lessons.id')
                ->join('modules', 'modules.id', '=', 'lessons.module_id')
                ->where('lessons.module_id', '<>', $this->id)
                ->where('tests.course_id', '=', $this->course_id)
                ->orderBy('modules.order')
                ->orderBy('lessons.order')
                ->get();
    }

    /**
     * Determine if a module is locked for the current user
     * @return bool
     */
    public function isLocked()
    {
        if ($this->locked_by_test && user() && !user()->isAdmin) {
            //get test result
            $test = Test::find($this->locked_by_test);
            if (!$test->userHasPassed(user())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getOrderedLessons()
    {
        return $this->lessons()->whereNull('removed_at')->orderBy('order');
    }

    public static function getNextOrderValueForModule($courseId)
    {
        $order = DB::table('modules')
            ->select('order')
            ->whereCourseId($courseId)
            ->orderBy('order', 'DESC')
            ->limit(1)
            ->get();

        if ($order->count()) {
            return (int) $order[0]->order + 1;
        }

        return 1;
    }

    /**
     * Returns the percentage of completed lessons in the module.
     *
     * @return int
     */
    public function getProgress()
    {
        static $moduleProgress;

        if (!user()) {
            return 0;
        }

        if (isset($moduleProgress[$this->id])) {
            return $moduleProgress[$this->id];
        }

        $lessons = "
            select count(*)
            from lessons l
            where l.module_id = :module
            and l.status = 1";
        $completed = "$lessons and exists
            (select * from lesson_subscriptions ls where l.id = ls.lesson_id and user_id = :user)";
        $tests = "
            select count(*)
            from tests t
            where t.after_lesson_id IN ($lessons)
            and t.status = 1";
        $passed = "$tests and exists (select * from test_users tu
              where t.id = tu.test_id and user_id = :user and mark >= 75)";
        $data = DB::select(
                "select (($completed) + ($passed)) / (($lessons) + ($tests)) * 100 as progress", ['module' => $this->id, 'user' => user()->id]
        );

        return $moduleProgress[$this->id] = number_format($data[0]->progress);
    }

    /**
     * Returns the index of the current module relative to the course it belongs to.
     *
     * @return int
     */
    public function getIndex()
    {
        $modules = (new CourseResources($this->course))
            ->modulesWithCountersAndProgress()
            ->get();

        foreach ($modules as $i => $module) {
            if ($module->slug == $this->slug) {
                return $i + 1;
            }
        }

        return 0;
    }

    public function getNext()
    {
        return $this->course->modules()
                ->enabled()
                ->where('order', '>', $this->order)
                ->orderBy('order')
                ->first();
    }

    public function getPrevious()
    {
        return $this->course->modules()
                ->enabled()
                ->where('order', '<', $this->order)
                ->orderBy('order', 'desc')
                ->first();
    }
}
