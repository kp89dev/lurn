<?php

namespace App\Console\Commands\Tasks;

use App\Models\CourseSubscriptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\StudentCount;

class UpdateStudentCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:count_students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the student counts for courses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $course_ids = Course::enabled()
            ->pluck('id');
        
        if(count($course_ids)) {
            foreach($course_ids as $course_id) {
                $studentCount = CourseSubscriptions::where('course_id', $course_id)->count();

                StudentCount::updateOrCreate(['id' => $course_id], ['students' => $studentCount]);
            }
        }
    }
}
