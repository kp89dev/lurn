<?php

namespace App\Http\Controllers\Admin\Workflows;

use App\Models\Workflows\Workflow;
use App\Http\Controllers\Controller;

class EmailStatsController extends Controller
{
    public function index(Workflow $workflow)
    {
        $stats = $workflow->emailStats()->paginate(25);

        return view('admin.workflows.email-stats', compact('workflow', 'stats'));
    }
}
