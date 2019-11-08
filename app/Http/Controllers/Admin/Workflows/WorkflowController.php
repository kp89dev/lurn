<?php
namespace App\Http\Controllers\Admin\Workflows;

use App\Http\Controllers\Controller;
use App\Models\Workflows\Workflow;
use App\Services\Workflows\View\ActionCollection;
use App\Services\Workflows\View\ConditionCollection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.role.auth:workflows,read')
            ->only('index');
        $this->middleware('admin.role.auth:workflows,write')
            ->only('create', 'store', 'edit', 'update', 'destroy', 'update-status');
    }

    public function index()
    {
        $workflows = Workflow::orderBy('id', 'DESC')->simplePaginate(20);

        return view('admin.workflows.index', compact('workflows'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $workflow = new class() {
            public $title;
            public $id;
            public $name;
            public $workflow = [[
                "type"       => 'enroll',
                "conditions" => []
            ]];
            public $goal     = [
                "type"       => "goal",
                "conditions" => []
            ];
        };

        $conditions = json_encode((new ConditionCollection())->getRepresentation());
        $actions    = json_encode((new ActionCollection())->getRepresentation());

        return view('admin.workflows.create-edit', compact('workflow', 'conditions', 'actions'));
    }

    public function store(Request $request)
    {
        if ($validation = $this->validateWorkflow($request) instanceof Response) {
            return $validation;
        }

        if ((int) $request->id > 0) {
            $model = Workflow::find($request->id);
        } else {
            $model = new Workflow();
        }

        $model->fill([
            'name'     => $request->get('name') ?? 'workflow',
            'goal'     => $request->get('goal'),
            'enroll'   => $request->get('workflow')[0],
            'workflow' => array_slice($request->get('workflow'), 1, count($request->get('workflow')), true)
        ])->save();

        return response()->json(['id' => $model->id]);
    }

    public function edit(Workflow $workflow)
    {
        $conditions = json_encode((new ConditionCollection())->getRepresentation());
        $actions    = json_encode((new ActionCollection())->getRepresentation());

        $workflow->workflow = [$workflow->enroll] + $workflow->workflow;

        return view('admin.workflows.create-edit', compact('workflow', 'conditions', 'actions'));
    }

    public function updateStatus(Request $request)
    {
        $workflow = Workflow::findOrFail($request->workflow);
        $workflow->status = !$workflow->status;
        $workflow->save();

        return response()->json(['OK']);
    }

    private function validateWorkflow($request)
    {
        $this->validate($request, [
            'goal'     => 'required|array',
            'goal.conditions' => 'required|array',
            'workflow' => 'required|array',
            'workflow.0.conditions' => 'required|array',
            'name' => 'required'
        ]);

        $goalValidationResult = (new ConditionCollection())->isValid($request->json()->get('goal')['conditions']);

        if (is_array($goalValidationResult)) {
            return response()->json($goalValidationResult, 422);
        }

        $workflowVars = collect($request->json()->get('workflow'));
        foreach ($workflowVars as $node) {
            switch($node['type']):
                case 'enroll':
                case 'ifelse':
                    $validationResult = (new ConditionCollection())->isValid($node['conditions']);
                    break;
                case 'action':
                    $validationResult = (new ActionCollection())->isValid($node);
                    break;
            endswitch;

            if (is_array($validationResult)) {
                return response()->json($goalValidationResult, 422);
            }
        }
    }
}
