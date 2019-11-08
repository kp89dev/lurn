<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class FixPermissionsForCop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:fixcoppermissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes the permissions for th CoP 10k Formula Bonuses';

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
        $originalId = $this->ask('Original Course Id:');
        
        $newId = $this->ask('New Course Id:');
        
        $original = Course::findOrFail($originalId);
        $new = Course::findOrFail($newId);
        
        $subscriptions = DB::table('course_subscriptions')->where('course_id', '=', $originalId)->get();
        
        $count = 0;
        foreach($subscriptions as $s) {
            $newSubscription = clone($s);
            $newSubscription->course_id = $new->id;
            $newSubscription->id = null;
            DB::table('course_subscriptions')->insert((array) $newSubscription);
            $count++;
            
        }
        $this->info('Added '.$count.' users to '.$new->title);
    }
}
