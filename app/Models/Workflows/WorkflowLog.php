<?php
namespace App\Models\Workflows;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkflowLog extends Model
{
    protected $guarded = ['id'];

    public static function store($workflowId, $query, $column)
    {
        $hash = md5($query);
        DB::insert(
            'INSERT IGNORE INTO workflow_logs (workflow_id, query, scope, hash, created_at, updated_at) VALUES (?, ?, ?, ?, now(), now())',
            [$workflowId, $query, $column, $hash]
        );
    }
}

