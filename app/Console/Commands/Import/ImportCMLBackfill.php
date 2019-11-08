<?php

namespace App\Console\Commands\Import;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportCMLBackfill extends Command
{
    use ConnectionList;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cmlfix {connection : The name of the database connection we are importing from. Defined in the config.} {name? : The new containers name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes import error from course and lesson subscriptions bug';
    
    protected $conn, $mysql_now;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mysql_now = Carbon::now()->format('Y-m-d H:i:s');
        
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->conn = $this->argument('connection');
        
        if($this->conn == 'all') {
            $this->importAll();
            exit();
        }
        
       //get course subscriptions
        $this->batchTranslateTableNR('course_subscriptions', 'course_subscriptions', 'course_id', 'courses', 'course_id');
        //get lesson subscriptions
        $this->batchTranslateTableNR('course_lesson_subscriptions', 'lesson_subscriptions', 'lesson_id', 'lessons', 'lesson_id');
        
    }
    
    protected function truncateTables() {
        DB::table('course_subscriptions')->truncate();
        DB::table('lesson_subscriptions')->truncate();
    }
    
    protected function batchTranslateTableNR($table, $new_table, $key, $type, $order_column) {
        //get the lessons for this connections...
        $ids = DB::table('cml_id_map')
            ->select('new_id', 'old_id')
            ->where('type', '=', $type)
            ->where('connection', '=', $this->conn)
            ->get();
        
        foreach($ids as $id) {
            $pages = 0;
            
            $new_id = $id->new_id;
            
            $old_query = DB::connection($this->conn)
                ->table($table)
                ->offset(0)
                ->limit(750)
                ->where($key, '=', $id->old_id)
            ;
            
            $olds = $old_query->get();
            
            
            while($olds->count()){
                $inserts = [];
                foreach($olds as $old) {
                    $old->$key = $new_id;
                    $old->from_table = $this->conn;
                  
                    $old = $this->adjustProps(get_object_vars($old), []);
                    unset($old['id']);
                    $inserts[] = $old;
                }
                
                DB::table($new_table)
                    ->insert($inserts);

                $pages++;
                $offset = $pages * 750;;
                unset($olds);
                $olds = $old_query->offset($offset)
                    ->get();
            }
        }
        $this->info('Translated lesson_subscriptions from '.$this->connectionList[$this->conn]);
    }
    
    protected function adjustProps($properties, array $skips) {
        $newProps = array();
        $keyChanges = [
            'last_modified' => 'updated_at',
            'lesson_display' => 'drip_delay'
        ];
        foreach($properties as $key => $value) {
            if(in_array($key, $skips)) {
                continue;
            }
            $new_key = snake_case($key);
            
            if(array_key_exists($new_key, $keyChanges)) {
                $new_key = $keyChanges[$new_key];
            }
            
            $newProps[$new_key] = $value;
            
        }
        
        return $newProps;
    }
    
    protected function importAll() 
    {
        $this->truncateTables();
        foreach($this->connectionList as $conn=>$name) {
            $this->call('import:cmlfix', [
                'connection' => $conn,
                'name' => $name,
            ]);
            $this->info('Completed rectification of '.$name);
        }

    }

}
