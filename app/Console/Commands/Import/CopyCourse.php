<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use App\Models\Course;

class CopyCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:copycourse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy a course';

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
        $originalId = $this->ask('Target Course Id:');
        
        $original = Course::find($originalId);
        
        $newCourse =$original->replicate();
        $newCourse->setTitleAttribute($newCourse->title);
        
        $newCourse->save();
        
        $modules = $original->modules;
        
        if(count($modules)) {
            foreach($modules as $module) {
                $this::info('Module');
                $newModule = $module->replicate();
                $newModule->course_id = $newCourse->id;
                $newModule->save();
                
                $lessons = $module->lessons;
                
                if(count($lessons)) {
                    foreach($lessons as $lesson) {
                        
                        $newLesson = $lesson->replicate();
                        $newLesson->module_id = $newModule->id;
                        
                        $newLesson->save();
                    }
                }
            }
        }
    }
}
