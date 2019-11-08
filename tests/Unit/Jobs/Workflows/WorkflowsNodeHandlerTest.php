<?php
namespace Unit\Jobs\Workflows;

use App\Jobs\Workflows\HandleWorkflowEnrollemnt;
use App\Models\UserLogin;
use App\Models\UserSetting;
use App\Models\Workflows\Workflow;
use App\Services\Workflows\Backend\ConditionParser;
use Illuminate\Database\Query\Builder;

class WorkflowsNodeHandlerTest extends \TestCase
{
    /**
     * @test
     * @group workflows
     */
    public function workflow_enrollment_stops_when_workflow_has_been_disabled()
    {
        $workflow = factory(Workflow::class)->make(['status' => 0]);

        $handler = new HandleWorkflowEnrollemnt($workflow);
        $result = $handler->handle();

        self::assertNull($result);
    }

    /**
     * @test
     * @group workflows
     */
    public function workflow_enrolls_correctly_with_a_simple_condition()
    {
        $conditionParserMock = $this->createMock(ConditionParser::class);
        $builder = $this->createMock(Builder::class);

        $conditionParserMock->expects(self::once())->method('run')->willReturn($builder);
        $builder->expects(self::once())
            ->method('toSql')
            ->willReturn('SELECT user_id as id, MAX(created_at) last_login_time
             FROM `user_logins` 
             GROUP BY user_id 
             HAVING last_login_time < "'. date('Y-m-d H:i:s', strtotime("-5 minute")) .'"');
        $builder->expects(self::once())
            ->method('getBindings')
            ->willReturn([]);

        $this->app->bind(ConditionParser::class, function($app) use ($conditionParserMock) {
            return $conditionParserMock;
        });

        $userLogin = factory(UserLogin::class)->create(['created_at' => date('Y-m-d H:i:s', strtotime("-10 minute"))]);
        $workflow = factory(Workflow::class)->create(['status' => 1]);
        factory(UserSetting::class)->create([
            'user_id'         => $userLogin->user_id,
            'receive_updates' => 1
        ]);

        $handler = new HandleWorkflowEnrollemnt($workflow);
        $handler->handle();

        self::assertDatabaseHas("user_workflows", [
            'user_id'     => $userLogin->user_id,
            'workflow_id' => $workflow->id
        ]);
    }
}
