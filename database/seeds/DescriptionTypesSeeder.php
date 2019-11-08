<?php

namespace Seeders;

use App\Models\DescriptionType;
use Illuminate\Database\Seeder;

class DescriptionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('course-custom-description-types') as $type => $description) {
            DescriptionType::firstOrCreate([
                'name' => strtolower($type),
                'description' => $description,
            ]);
        }
    }
}
