<?php
namespace App\Services\Workflows\Backend\Node;

class NextNodeCalculator
{
    public static function getFrom($currentNode, $branch = null)
    {
        if (strpos($currentNode, '.') === false && is_null($branch)) {
            return $currentNode + 1;
        }

        if (! is_null($branch)) {
            return $currentNode. ".nodes_" . ($branch ? 'true': 'false') . '.' . 0;
        }

        $parts = explode('.', $currentNode);
        $lastPart = (end($parts) + 1);

        array_pop($parts);
        array_push($parts, $lastPart);

        return implode('.', $parts);
    }
}
