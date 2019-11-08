<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportUsers extends Command
{
    use ConnectionList;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users {connection : The name of the database connection we are importing from. Defined in the config.} {name? : The new containers name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports users from a given table';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('connection') == 'all') {
            $this->importAll();

            exit();
        }


        if (! $name = $this->argument('name')) {
            $name = $this->ask('The name?');
        }

        $this->importOne($this->argument('connection'), $name);
    }

    protected function importAll()
    {
        foreach ($this->connectionList as $connName => $value) {
            $this->importOne($connName, $value);
        }
    }

    protected function importOne($connection, $name)
    {
        $config = config('database.connections.' . $connection);

        if (is_null($config)) {
            $this->warn("Ignoring $connection was not found in database.php");

            return;
        }

        $this->info("Importing using $connection");

        $offset = 0;

        while(true) {
            $olds = DB::connection($connection)
                        ->table('users')
                        ->select('*')
                        ->offset($offset)
                        ->limit(1000)
                        ->get();

            if (!$olds->count()) {
                $this->info("Finished importing $connection");
                return;
            }

            $offset += 1000;

            foreach($olds as $index => $old) {
                $old = (array) $old;
                try {
                    DB::table('users_import_all')->insert([
                        'user_id'             => $old['id'],
                        'role_id'             => $old['role_id'],
                        'connection'          => $connection,
                        'name'                => $old['name'],
                        'email'               => $old['email'],
                        'password'            => $old['password'],
                        'md5password'         => $old['md5password'],
                        'salt'                => $old['salt'],
                        'description'         => $old['description'],
                        'status'              => $old['status'],
                        'infusion_order_id'   => $old['infusion_order_id'],
                        'infusion_contact_id' => $old['infusion_contact_id'],
                        'timezone'            => $old['timezone'],
                        'updated_at'          => $old['updated_at'],
                        'created_at'          => $old['created_at'],
                        'expiry_date'         => $old['expiry_date'],
                        'unsubscribe'         => $old['unsubscribe'],
                        'settings'            => $old['settings']
                    ]);
                } catch (\Exception $e) {
                    $this->info("Skipped: " . $e->getMessage());
                }
            }
        }
    }
}
