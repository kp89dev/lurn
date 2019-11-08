<?php
/**
 * Date: 3/15/18
 * Time: 1:32 PM
 */

namespace App\Commands\Imports;

use App\Commands\Base;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class BadgeImport extends Base
{
    protected $connection;

    protected $config;

    protected $error;

    /** @var DatabaseManager */
    private $db;

    /** @var Collection|null */
    protected $importing;

    /**
     * BadgeImport constructor.
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /** @var Command */
    protected $command;

    public function process()
    {
        $this->setConfig();

        if ($this->error) {
            $this->command->error("Aborting: {$this->error}");
        }
    }

    protected function import()
    {
        if (!$this->error) {
            $this->command->info("Importing badges using {$this->connection}");

            $this->setImporting();
            $this->beginDatabaseTransaction();
        }
    }

    protected function importImporting()
    {
        if (!$this->error) {
            foreach ($this->importing as $import) {

            }
        }
    }

    protected function beginDatabaseTransaction()
    {
        if (!$this->error) {
            try {
                $this->db->beginTransaction();
            } catch (\Exception $e) {
                $message = catch_and_return('There was a problem starting a transaction.', $e);
                $this->error = $message;
            }
        }
    }

    protected function setImporting()
    {
        if (!$this->error) {
            $this->importing = $this->db->connection($this->connection)
                ->table('badges')
                ->select('*')
                ->get();
        }
    }

    /**
     * @param mixed $connection
     * @return BadgeImport
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     *
     */
    protected function setConfig()
    {
        $this->config = config('database.connections.' . $this->connection);

        if (is_null($this->config)) {
            $this->error = "Ignoring {$this->connection} was not found in database.php";
        }
    }

    /**
     * @param Command $command
     * @return BadgeImport
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }
}