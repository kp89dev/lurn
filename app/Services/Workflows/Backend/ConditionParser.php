<?php
namespace App\Services\Workflows\Backend;

use Illuminate\Database\Query\Builder;

class ConditionParser
{

    /**
     * @type Builder
     */
    private $query;

    /**
     * @type array
     */
    private $conditions;

    public function __construct(Builder $query, array $conditions)
    {
        $this->query = $query;
        $this->conditions = $conditions;
    }

    public function run()
    {
        $groupedConditions = $this->groupConditions($this->conditions['conditions']);
        $queries = [];

        foreach ($groupedConditions as $cnd) {
            $query = clone $this->query;
            foreach ($cnd as $c) {
                $class = $this->getClassName($c['key']);

                $object = new $class;

                $object->join($query, $c['values']);
                $object->where($query, $c['values']);
            }

            $queries[] = $query;
        }

        $this->query = $queries[0];
        $iMax = count($queries);

        for ($i = 1; $i < $iMax; $i++) {
            $this->query->union($queries[$i]);
        }

        return $this->query;
    }

    protected function getClassName($classFqn)
    {
        return 'App\Services\Workflows\Backend\Selectors\\' . class_basename($classFqn);
    }

    protected function groupConditions($ungroupedConditions)
    {
        $groupedConditions = [];
        $i = 0;

        while ($current = current($ungroupedConditions)) {
            if ($current['type'] === 'or') {
                $i++;
            }

            $groupedConditions[$i][] = $current;
            next($ungroupedConditions);
        };

        return $groupedConditions;
    }
}
