<?php

namespace Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonTableSeeder extends Seeder
{
    public function run()
    {
        Module::all()->each(function ($module) {
            factory(Lesson::class, rand(3, 10))->create(['module_id' => $module->id]);
        });
    }
}
