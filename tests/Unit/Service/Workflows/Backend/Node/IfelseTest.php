<?php
namespace Unit\Service\Workflows\Backend\Node;

use App\Models\Workflows\UserWorkflow;
use App\Services\Workflows\Backend\Node\Ifelse;
use App\Services\Workflows\Backend\UserSpecificConditionChecker;

class IfelseTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     * @dataProvider data
     */
    public function ifelseReturnsCorrectNextNode($return, $expectedNextStep)
    {
        $userWorkflow = factory(UserWorkflow::class)->create([
            'next_step' => 3,
            'next_step_time' => null
        ]);

        $this->app->bind(UserSpecificConditionChecker::class, function () use ($return) {
            $conditionParserMock = $this->createMock(UserSpecificConditionChecker::class);
            $conditionParserMock->expects(self::once())->method('passes')->willReturn($return);

            return $conditionParserMock;
        });

        $action = new Ifelse();
        $action->execute($userWorkflow, [
            ['delay' => 5, 'delayUnit' => 'days']
        ]);

        $this->assertDatabaseHas('user_workflows', [
            'user_id'     => $userWorkflow->user_id,
            'workflow_id' => $userWorkflow->workflow_id,
            'next_step'   => $expectedNextStep
        ]);
    }

    public function data()
    {
        return [
            [true, '3.nodes_true.0'],
            [false, '3.nodes_false.0']
        ];
    }
}
