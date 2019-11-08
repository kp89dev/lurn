<?php
namespace App\Console\Commands\Import;

use App\Models\Course;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportBadges extends Command
{
    use ConnectionList;

    protected $signature = "import:badges {connection : Database connection where to import from}";

    protected $description = "Imports badges and each gained badge";

    public function handle()
    {
        if ($this->argument('connection') === 'all') {
            $this->importAll();

            exit();
        }

        $this->importOne($this->argument('connection'));
    }

    protected function importAll()
    {
        foreach($this->connectionList as $conn => $name) {
            $this->call('import:badges', [
                'connection' => $conn
            ]);

            $this->info('Completed transfer of '.$name);
        }
    }

    protected function importOne($connection)
    {
        $config = config('database.connections.' . $connection);

        if (is_null($config)) {
            $this->error("Ignoring $connection was not found in database.php");

            return;
        }

        $this->info("Importing badges using $connection");


        $olds = DB::connection($connection)
            ->table('badges')
            ->select('*')
            ->get();

        DB::beginTransaction();

        foreach($olds as $index => $old) {
            $old = (array) $old;
            try {
                $newId = DB::table('badges')->insertGetId([
                    'title'     => $old['title'],
                    'content'   => $old['description'],
                    'course_id' => $this->getCourseId($connection)
                ]);

                DB::table('cml_id_map')->insert([
                    'old_id'     => $old['id'],
                    'new_id'     => $newId,
                    'type'       => 'badges',
                    'connection' => $connection
                ]);
            } catch (\Throwable $e) {
                $this->error("Aborted " . $e->getMessage());
                DB::rollBack();
                exit();
            }
        }
        $this->info('Importing gained badges');

        try {
            DB::connection($connection)->table('badge_users')->orderBy('id')->chunk(500, function($rows) use ($connection) {
                foreach ($rows as $r):
                    $data = (array) $r;
                    unset($data['id']);

                    DB::table('user_badges_import_all')->insert(
                        $data
                        + ['connection' => $connection]
                    );
                endforeach;
            });
        } catch (\Throwable $e) {
            $this->error("Aborted " . $e->getMessage());
            DB::rollBack();
            exit();
        }

        DB::commit();
        $this->info('Gained badges Imported for '. $connection);
    }

    protected function getCourseId($connection)
    {
        static $courseChosen = [];

        if (isset($courseChosen[$connection])) {
            return $courseChosen[$connection];
        }

        $results = DB::table('cml_id_map')
                        ->where('connection', $connection)
                        ->where('type', 'courses')
                        ->get();

        if (! $results->count()) {
            $this->error("Course for badges couldn't be found. Aborting.");

            exit();
        }

        if ($results->count() == 1) {
            return $results->first()->new_id;
        }

        if ($results->count() > 1) {
            $options = [];

            array_map(function ($value) use (&$options) {
                $options[$value->new_id] = Course::find($value->new_id)->title;
            }, $results->toArray());

            $course = $this->choice('Multiple courses found. Which one to assign badges ?', $options);

            $courseChosen[$connection] = array_search($course, $options, false);

            if ($courseChosen[$connection] === false) {
                $this->error('Problem with the choice');

                exit();
            }

            return $courseChosen[$connection];
        }
    }
}
