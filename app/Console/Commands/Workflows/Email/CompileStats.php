<?php

namespace App\Console\Commands\Workflows\Email;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Workflows\WorkflowStatistic;

class CompileStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:compile-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Turns raw statistics into a summary table';

    protected $periods = ['30 DAY', '60 DAY', '90 DAY', '6 MONTH', '1 YEAR'];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $results = [];

        foreach($this->periods as $period) {
            $query = DB::table('email_statuses')
                ->select(
                    'workflow_id AS wfid',
                    'step AS stp',
                    DB::raw('(SELECT count(*) as a FROM email_statuses where created_at > DATE_SUB(CURDATE(), INTERVAL ' . $period . ') and status >= 0 and workflow_id = wfid and step = stp) as send'),
                    DB::raw('(SELECT count(*) as a FROM email_statuses where updated_at > DATE_SUB(CURDATE(), INTERVAL ' . $period . ') and status >= 50 and workflow_id = wfid and step = stp) as delivery'),
                    DB::raw('(SELECT count(*) as a FROM email_statuses where updated_at > DATE_SUB(CURDATE(), INTERVAL ' . $period . ') and status >= 100 and workflow_id = wfid and step = stp) as `open`'),
                    DB::raw('(SELECT count(*) as a FROM email_statuses where updated_at > DATE_SUB(CURDATE(), INTERVAL ' . $period . ') and status = 200 and workflow_id = wfid and step = stp) as click'),
                    DB::raw('(SELECT count(*) as a FROM email_statuses where updated_at > DATE_SUB(CURDATE(), INTERVAL ' . $period . ') and status = 25 and workflow_id = wfid and step = stp) as bounce')
                )
                ->groupBy(['workflow_id', 'step']);
            
            $results[$period] = $query->get();
        }
        
        foreach ($results as $period=>$res) {
            foreach ($res as $stat) {
                WorkflowStatistic::create([
                    'workflow_id'   => $stat->wfid,
                    'step'          => $stat->stp,
                    'period'        => $period,
                    'send'          => $stat->send,
                    'delivery'      => $stat->delivery,
                    'open'          => $stat->open,
                    'click'         => $stat->click,
                    'bounce'        => $stat->bounce
                ]);
            }
        }
        
    }
}
