<?php

namespace Tests\Unit\Console\Commands\Tasks;

use App\Models\Course;
use App\Models\CourseSubscriptions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;

class UpdateStudentCountsTest extends \TestCase
{
    /**
     * @test
     */
    public function StudentCounts()
    {
        $course = factory(Course::class)->create(['status' => 1]);
        factory(CourseSubscriptions::class)->create(['course_id' => $course->id]);

        $this->artisan('task:count_students');

        $this->assertDatabaseHas('student_counts', ['id' => $course->id, 'students' => 1]);
    }
}
