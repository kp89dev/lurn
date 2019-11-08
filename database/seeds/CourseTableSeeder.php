<?php

namespace Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class ContainersTableSeeder extends Seeder
{
    public function run()
    {
        factory(Course::class, 5)->create();
    }
}
