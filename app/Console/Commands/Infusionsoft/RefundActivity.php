<?php
/**
 * Date: 3/15/18
 * Time: 3:49 PM
 */

namespace App\Console\Commands\Infusionsoft;

use App\Commands\Infusionsoft\Refunds;
use App\Jobs\Infusionsoft\ResponseHandlerTrait;
use App\Models\Queries\ProcessRefunds;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RefundActivity extends Command
{
    use ResponseHandlerTrait;

    protected $signature = 'course:refunds {debug? : flag to allow debugging messages}';
    protected $description = 'Queries Infusionsoft to check for any issued refunds in the last minute';

    /** @var Refunds */
    protected $refunds;

    /** @var ProcessRefunds */
    protected $query;

    /**
     * RefundActivity constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->refunds = app(Refunds::class);
        $this->query = app(ProcessRefunds::class);
    }

    public function handle()
    {
        $identifier = sha1(time());
        $date = Carbon::now()->subDays(1);
        $this->refunds->setIdentifier($identifier)->setCommand($this)->setDate($date)->process();
        $this->query->setRefundsHandler($this->refunds)->process();
    }
}