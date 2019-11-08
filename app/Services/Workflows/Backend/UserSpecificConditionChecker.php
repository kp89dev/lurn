<?php
namespace App\Services\Workflows\Backend;

use Illuminate\Support\Facades\DB;

class UserSpecificConditionChecker
{
    /**
     * @type int
     */
    private $userId;

    /**
     * @type array
     */
    private $nodeConditions;

    /**
     * @type string
     */
    public $sqlQuery;

    /**
     * @type array
     */
    public $sqlBindings;
    
    public function __construct(int $userId, array $nodeConditions)
    {
        $this->userId = $userId;
        $this->nodeConditions = $nodeConditions;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        $query = DB::table('users')->select("users.*");
        $query = (new ConditionParser($query, $this->nodeConditions))->run();

        $this->sqlQuery = 'SELECT * FROM (' . $query->toSql() . ') as user_result ' .
                    'WHERE user_result.id = ' . $this->userId;
        $this->sqlBindings = $query->getBindings();

        return count(DB::select($this->sqlQuery, $this->sqlBindings)) > 0;
    }
}
