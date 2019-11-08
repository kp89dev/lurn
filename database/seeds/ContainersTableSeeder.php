<?php

namespace Seeders;

use App\Models\CourseContainer;
use Illuminate\Database\Seeder;

class CourseTableSeeder extends Seeder
{
    public function run()
    {
        factory(CourseContainer::class, 5)->create();
    }
}
