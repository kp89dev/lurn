<?php
namespace Tests\Unit\Policies;

use App\Models\Course;
use App\Models\CourseTool;
use App\Models\User;
use App\Policies\ToolPolicies;

class ToolPoliciesTests extends \TestCase
{
    /**
     * @test
     * @dataProvider toolNames
     */
    public function tool_dosent_have_access_on_canceled_course($toolName, $method)
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        factory(CourseTool::class)->create([
            'course_id' => $course->id,
            'tool_name' => $toolName
        ]);
        $user->courses()->attach($course, [
            'cancelled_at' => new \DateTime()
        ]);

        $policy = new ToolPolicies();
        self::assertFalse($policy->$method($user));
    }

    /**
     * @test
     * @dataProvider toolNames
     */
    public function tool_access_course($toolName, $method)
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        factory(CourseTool::class)->create([
            'course_id' => $course->id,
            'tool_name' => $toolName
        ]);
        $user->courses()->attach($course);

        $policy = new ToolPolicies();
        self::assertTrue($policy->$method($user));
    }

    /**
     * @test
     * @dataProvider toolNames
     */
    public function tool_access_denied_when_course_does_not_have_the_tool_defined($toolName, $method)
    {
        $user = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $user->courses()->attach($course);

        $policy = new ToolPolicies();
        self::assertFalse($policy->$method($user));
    }

    public function toolNames()
    {
        return [
          ['Niche Detective', 'nicheDetective'],
          ['Launchpad', 'launchpad'],
          ['Business Builder', 'businessBuilder']
        ];
    }
}
