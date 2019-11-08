<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;

class Sluggify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sluggify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the slugs for imported resources';

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
        $types = ['App\Models\Course', 'App\Models\Module', 'App\Models\Lesson'];
        
        foreach($types as $type) {
            $this->info('Sluggifying ' . $type);
            
            $sluggables = $type::all();
            
            foreach($sluggables as $slug) {
                if($slug->slug == '') {
                    $slug->setTitleAttribute($slug->title);
                    $slug->save();
                }
            }
        }
    }
    
   
}
