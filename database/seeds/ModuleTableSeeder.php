<?php

namespace Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    public function run()
    {
        Course::all()->each(function ($course) {
            factory(Module::class, rand(3, 10))->create(['course_id' => $course->id]);
        });
    }
}
