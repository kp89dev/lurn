<?php

namespace Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ContainersTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        
        $this->call(ModuleTableSeeder::class);
        $this->call(LessonTableSeeder::class);
        
        $this->call(EventsTableSeeder::class);
        $this->call(TestsTableSeeder::class);
    }
}
