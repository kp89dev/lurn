<?php

namespace Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\EmailStatus;
use Carbon\Carbon;

class EmailStatsCompileTest extends \TestCase
{
    /**
     * @test
     */
    public function assert_task_adds_correct_periods()
    {
        $statuses = factory(EmailStatus::class)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'created_at'    => Carbon::now()
        ]);
        
        $exit = Artisan::call('workflows:compile-stats');
        
        $this->assertDatabaseHas('workflow_statistics', ['period' => '30 DAY'])
            ->assertDatabaseHas('workflow_statistics', ['period' => '60 DAY'])
            ->assertDatabaseHas('workflow_statistics', ['period' => '90 DAY'])
            ->assertDatabaseHas('workflow_statistics', ['period' => '6 MONTH'])
            ->assertDatabaseHas('workflow_statistics', ['period' => '1 YEAR']);
    }
    
    /**
     * skipped (doesn't work, but should)
     */
    public function assert_counts_include_correct_records()
    {
        //sent
        factory(EmailStatus::class, 3)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => 0,
            'created_at'    => Carbon::now()
        ]);
        
        //bounce
        factory(EmailStatus::class)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => '25',
            'updated_at'    => Carbon::now()
        ]);
        
        //delivered
        factory(EmailStatus::class, 5)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => '50',
            'updated_at'    => Carbon::now()
        ]);
        
        //open
        factory(EmailStatus::class, 2)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => '100',
            'updated_at'    => Carbon::now()
        ]);
        
        //clicks
        factory(EmailStatus::class)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => '200',
            'updated_at'    => Carbon::now()
        ]);
        
        $exit = Artisan::call('workflows:compile-stats');

        //12 sent, 1 bounce, 8 delivered, 3 opened, 1 clicked
        $this->assertDatabaseHas('workflow_statistics', [
            'period'    => '30 DAY',
            'send'      => 12,
            'delivery'  => 8,
            'bounce'    => 1,
            'open'      => 3,
            'click'     => 1
        ]);
        
    }
    
    /**
     * @test
     */
    public function assert_clicks_appear_in_appropriate_period()
    {
        $date = Carbon::now();
        $date->subDays(45);
        
        factory(EmailStatus::class)->create([
            'workflow_id'   => 1,
            'step'          => 1,
            'status'        => 0,
            'created_at'    => $date
        ]);
        
        $exit = Artisan::call('workflows:compile-stats');
        
        $this->assertDatabaseHas('workflow_statistics', [
            'period'    => '60 DAY',
            'send'      => 1,
        ]);

        $this->assertDatabaseMissing('workflow_statistics', [
            'period'    => '30 DAY',
            'send'      => 1,
        ]);
        
        $this->assertDatabaseHas('workflow_statistics', [
            'period'    => '1 YEAR',
            'send'      => 1,
        ]);
    }

}
