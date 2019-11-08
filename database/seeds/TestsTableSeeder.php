<?php

namespace Seeders;

use App\Models\Test;
use App\Models\TestQuestion;
use App\Models\TestQuestionAnswer;
use Illuminate\Database\Seeder;

class TestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Test::class, 10)
            ->create()
            ->each(function (Test $t) {
                factory(TestQuestion::class, 15)
                ->create(['test_id' => $t->id])
                ->each(function (TestQuestion $tq) {
                    factory(TestQuestionAnswer::class, 5)
                    ->create(['question_id' => $tq->id]);
                });
            });
    }
}
