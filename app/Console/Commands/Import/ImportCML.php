<?php

namespace App\Console\Commands\Import;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportCML extends Command
{
    use ConnectionList;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cml {connection : The name of the database connection we are importing from. Defined in the config.} {name? : The new containers name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports course, modules, lessons, and their associated data';
    
    protected $conn, $mysql_now;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->mysql_now = (Carbon::now())->format('Y-m-d H:i:s');
        
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
        
        $containerName = (null !== $this->argument('name')) ? 
            $this->argument('name') : 
            $this->ask('Title of the new course container:');
        
        $containerId = DB::table('course_containers')->insertGetId(['title' => $containerName, 'created_at' => $this->mysql_now]);
        DB::table('cml_id_map')->insert(['type' => 'course_container', 'new_id' => $containerId, 'connection' => $this->conn]);
        
        //get courses
        $this->translateTable('courses', [], ['image', 'userId'], ['course_container_id' => $containerId]);
        //get course contents
        $this->translateTable('contents', ['course_id'=>'courses'], [], []);
        //geet modules
        $this->translateTable('modules', ['course_id'=>'courses'], ['icon'], ['hidden' =>0]);
        //get lessons
        $this->translateTable('lessons', ['module_id'=>'modules'], ['lesson_number', 'lesson_materials', 'summary', 'icon', 'password']);
        //get course lesson mataerials
        $this->translateTable('course_lesson_materials', ['lesson_id' => 'lessons']);
        //get tests
        $this->translateTable('tests', ['after_lesson_id' => 'lessons', 'course_id' => 'courses'], ['createdDate']);
        //get test questions
        $this->translateTable('test_questions', ['test_id' => 'tests'], ['refer_module_id']);
        //get test question answers
        $this->translateTable('test_question_answers', ['question_id' => 'test_questions'], ['test_id']);
        //get course certificates
        //$this->translateTable('course_certificates', ['course_id' => 'courses'], [], ['from_table' => $this->conn]);
        //get the test users
        $this->translateTable('test_users', ['test_id' => 'tests'], ['answer'], ['from_table' => $this->conn]);
       //get course subscriptions
        $this->batchTranslateTableNR('course_subscriptions', 'course_subscriptions', 'course_id', 'courses', 'course_id');
        //get lesson subscriptions
        $this->batchTranslateTableNR('course_lesson_subscriptions', 'lesson_subscriptions', 'lesson_id', 'lessons', 'lesson_id');
        
    }
    
    protected function translateTable($table, $keys = [], $skips = [], $new = []) {
        $knownKeys = array();
        
        if($table == 'course_lesson_subscriptions') {
            $this->translateLessonSubscription();
            return;
        }
        
        $olds = DB::connection($this->conn)->table($table)->select('*');
        
        $olds = $olds->get();
        
        foreach($olds as $index => $old) {
            $new_id = null;
            $knownKeys = [];
            $old = $this->adjustProps(get_object_vars($old), $skips);
            foreach($keys as $prop => $type) {
                if($table == 'lessons') {
                    $mappedKey = $this->fixLesson($old);
                    unset($old['course_id']);
                    $new_id = $mappedKey->new_id;
                } else {
                    if(array_key_exists($old[$prop], $knownKeys)) {
                        $new_id = $old[$prop];
                    } else{
                        $mappedKey = DB::table('cml_id_map')
                            ->where('connection', '=', $this->conn)
                            ->where('type', '=', $type)
                            ->where('old_id', '=', $old[$prop])
                            ->first();
                        if($mappedKey) {
                            $knownKeys[$old[$prop]] = $mappedKey->new_id;
                            $new_id = $mappedKey->new_id;
                        }
                    }
                }
                if(is_null($new_id) && 
                    (in_array( $table, [
                        'test_question_answers',
                        'lesson_subscriptions',
                        'test_questions',
                        'test_users']))) {
                    continue; //orphaned items
                }
                
                $old[$prop] = $new_id;

                if($table == 'lessons') {
                    unset($old['course_id']);
                }
            }
            foreach($new as $prop=>$value) {
                $old[$prop] = $value;
            }
            
            $oldId = $old['id'];
            unset($old['id']);
            
            $newId = DB::table($table)->insertGetId($old);
            if($table != 'lesson_subscriptions'){
                DB::table('cml_id_map')->insert([
                    'type' => $table, 
                    'connection' => $this->conn,
                    'old_id' => $oldId,
                    'new_id' => $newId
                ]);
            }
            
                //$this->info('Translated '.$table.' from '.$this->conn.' org ID: '.$oldId.' new ID: '.$newId);
        }//endforeach
        $this->info('Translated '.$table.' from '.$this->connectionList[$this->conn]);
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
        foreach($this->connectionList as $conn=>$name) {
            $this->call('import:cml', [
                'connection' => $conn,
                'name' => $name,
            ]);
            $this->info('Completed transfer of '.$name);
        }
        
        //Set drips on courses...
        $this->setDrips();
        //Set the slugs
        $this->call('import:sluggify');
    }
    
    protected function fixLesson($lesson) 
    {
        $mappedKey = DB::table('cml_id_map')
            ->where('connection', '=', $this->conn)
            ->where('type', '=', 'modules')
            ->where('old_id', '=', $lesson['module_id'])
            ->first();
        if($mappedKey) {
            return $mappedKey;
        }
        
        $moduleId = $this->getDefaultModuleId($lesson['course_id']);
        
        $obj = new \stdClass();
        $obj->new_id = $moduleId;
        
        return $obj;
    }
    
    protected function getDefaultModuleId($courseId)
    {
        $newCourseId =  DB::table('cml_id_map')
                    ->where('connection', '=', $this->conn)
                    ->where('type', '=', 'courses')
                    ->where('old_id', '=', $courseId)
                    ->get();
        $module = DB::table('modules')
            ->where('course_id', '=', $newCourseId[0]->new_id)
            ->where('title', '=', 'Default')
            ->first();
        if($module) {
            return $module->id;
        } else {
           $moduleId = DB::table('modules')->insertGetId([
                'course_id' => $newCourseId[0]->new_id,
                'title' => 'Default',
                'order' => 0,
                'status' => 0,
                'hidden' => 1,
                'type' => 'Module',
            ]);
           
           return $moduleId;
        }
    }
    
    protected function moduleExists($courseId, $modId)
    {
        $newCourseId =  DB::table('cml_id_map')
            ->where('connection', '=', $this->conn)
            ->where('type', '=', 'courses')
            ->where('old_id', '=', $courseId)
            ->get();
        $module = DB::table('modules')
            ->where('course_id', '=', $newCourseId[0]->new_id)
            ->where('title', '=', 'Default')
            ->first();
        if($module) {
            return true;
        }
        return false;
    }
    
    protected function setDrips() {
        $dripper_id = DB::table('lessons')
            ->select('course_id')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->where('lessons.drip_delay', '!=', 0)
            ->groupBy('course_id')
            ->get();
        
        foreach($dripper_id as $id) {
            $course = Course::find($id->course_id);
            $course->drip =1;
            $course->save();
            $this->info('Set drip on ' . $course->title);
        }
    }

}
