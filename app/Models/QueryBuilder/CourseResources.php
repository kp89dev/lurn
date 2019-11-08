<?php

namespace App\Models\QueryBuilder;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseResources
{
    protected $course;
    protected $data;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function get()
    {
        return $this->data;
    }

    public function modulesWithCountersAndProgress($module = null)
    {
        $moduleId = $module instanceof Module ? $module->id : $module;

        $dripClause = $this->course->drip ? "and (select datediff(now(), uc.created_at) from user_courses uc 
           where user_id = :user and course_id = :course and cancelled_at is null) >= drip_delay" : '';

        $allLessonsAndLinks = "
            select count(*)
            from lessons
            where module_id = m.id
              and status = 1
             $dripClause";
        $allTests = "
            select count(*)
            from tests t
            join lessons l
              on l.id = t.after_lesson_id
            where t.course_id = :course
              and l.module_id = m.id
              and t.course_id is not null
              and t.status = 1";
        $allLessons = "
            select count(*)
            from lessons l
            inner join modules as `mod`
               on mod.id = l.module_id
            where mod.course_id = :course
              and mod.id = m.id
              and mod.status = 1
              and l.status = 1
              and mod.type = 'Module'
              $dripClause";
        $completedLessons = "$allLessons and exists
            (select * from lesson_subscriptions ls where l.id = ls.lesson_id and user_id = :user)";
        $passedTests = "$allTests and exists
            (select * from test_users tu where t.id = tu.test_id and user_id = :user and mark > :passingMark)";
        $unlockedTests = "select count(*) from test_users
            where mark > :passingMark and test_id = locked_by_test and user_id = :user";
        $query = "
            select
              (($allLessons) + ($allTests)) as lessonsSidebarCounter,
              @lessons := ($allLessonsAndLinks) as lessons,
              @tests := ($allTests) as tests,
              coalesce(round( (($completedLessons) + ($passedTests)) / (@lessons + @tests) * 100 ), 0) as progress,
              title, slug, `type`, link, id, hidden, locked_by_test,
              ($unlockedTests) as unlocked
            from modules m
            where course_id = :course
              and m.status = 1
              " . ($moduleId ? 'and m.id = :moduleId' : '') . "
            order by `order`";

        $this->data = collect(DB::select($query, [
            'user'        => user() ? user()->id : 0,
            'course'      => $this->course->id,
            'passingMark' => TEST_PASSING_MARK,
            'moduleId'    => $moduleId,
        ]));

        return $this;
    }

    public function addLessons()
    {
        if (! $this->data instanceof Collection) {
            throw new \Exception("You need to get the modules first.");
        }

        $moduleIds = $this->data->pluck('id')->implode(',');

        if (! strlen($moduleIds)) {
            return $this;
        }

        $dripClause = $this->course->drip ? "and (select datediff(now(), uc.created_at) from user_courses uc where
            user_id = :user and course_id = :course and cancelled_at is null) >= drip_delay" : '';

        $query = "
            select l.id, l.slug, l.title, l.type, coalesce(l.link, 1) as link,
            module_id, t.id as testId, t.title as testTitle, coalesce(tu.mark, 0) as testMark, (
                select if (count(*), 'completed', '') from lesson_subscriptions ls
                where user_id = :user and lesson_id = l.id
            ) as completed
            from lessons l
            left join tests t
              on (t.after_lesson_id = l.id and t.status = 1)
            left join test_users tu
              on (tu.test_id = t.id and tu.user_id = :user)
            where l.status = 1
              and module_id in ($moduleIds)
              $dripClause
              order by `order`";

        $lessons = DB::select($query, [
            'user'   => user() ? user()->id : 1,
            'course' => $this->course->id,
        ]);

        foreach ($lessons ?? [] as $lesson) {
            $moduleId = $lesson->module_id;
            $groupedLessons[$moduleId][] = $lesson;
        }

        $this->attachLessonsToModules($groupedLessons ?? []);

        return $this;
    }

    protected function attachLessonsToModules($groupedLessons)
    {
        foreach ($this->data as $i => $module) {
            $module->orderedLessons = $groupedLessons[$module->id] ?? [];

            if ($this->hideOnboardingModule($module)) {
                continue;
            }
        }
    }

    protected function hideOnboardingModule($module)
    {
        $lessons = $module->orderedLessons;

        if ($module->slug == 'on-boarding' && count($lessons) && $lessons[0]->completed == 'completed') {
            return $module->hidden = 1;
        }

        return false;
    }
}
