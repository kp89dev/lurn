<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Services\Workflows\Backend\Node\NextNodeCalculator;
use TestCase;

class NextNodeCalculatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider data_provider
     * @group workflows
     */
    public function next_node_is_calculated_correctly($node, $branch, $result)
    {
        $nextNode = NextNodeCalculator::getFrom($node, $branch);

        self::assertEquals($result, $nextNode);
    }

    public function data_provider()
    {
        return [
            [0, null, 1],
            [1, null, 2],
            [2, true, '2.nodes_true.0'],
            [2, false, '2.nodes_false.0'],
            ['2.nodes_true.0', null, '2.nodes_true.1'],
            ['3.nodes_false.11', null, '3.nodes_false.12'],
            ['3.nodes_false.11.nodes_true.7', null, '3.nodes_false.11.nodes_true.8'],
            ['3.nodes_false.11.nodes_true.7', true, '3.nodes_false.11.nodes_true.7.nodes_true.0'],
        ];
    }
}
