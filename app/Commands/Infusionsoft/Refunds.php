<?php
/**
 * Date: 3/16/18
 * Time: 10:06 AM
 */

namespace App\Commands\Infusionsoft;

use App\Commands\Base;
use App\Models\InfusionsoftToken;
use App\Models\RefundTracker;
use App\Models\RefundTrackerHistory;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Infusionsoft\Infusionsoft;
use Infusionsoft\TokenExpiredException;

class Refunds extends Base
{
    /** @var Infusionsoft */
    protected $isAccount;

    protected $limit = 500;

    /** @var Collection */
    protected $refunds;

    /** @var Collection */
    protected $invoices;

    /** @var Carbon */
    protected $date;

    /** @var int */
    protected $page = 0;

    /** @var Command */
    protected $command;

    /** @var array */
    private $contacts;

    /** @var Exception|null */
    protected $thrownException;

    /** @var string */
    protected $activeIsAccount;

    /** @var string */
    protected $identifier;

    /** @var RefundTracker */
    protected $refundTracker;

    /** @var string */
    protected $errorMessage;

    public function process()
    {
        try {
            $this->addToTracker();
            $this->fillRefunds();
        } catch (Exception $e) {
            $this->handleException($e, 'There was a problem fetching the Infusionsoft refunds.');
            $this->failRefundTracker($this->errorMessage);
            $this->errorMessage = null;
        }
    }

    /**
     * @param string $activity
     * @param string|null $errorMessage
     */
    public function addRefundTrackerHistory(string $activity, string $errorMessage = null)
    {
        $this->refundTracker->histories()->create([
            'activity' => $activity,
            'error_message' => $errorMessage,
        ]);
    }

    protected function addToTracker()
    {
        $this->refundTracker = RefundTracker::create([
            'identifier' => $this->identifier,
            'started_at' => Carbon::now(),
        ]);
    }

    /**
     * @param string $failedReason
     */
    public function failRefundTracker(string $failedReason)
    {
        $this->refundTracker->update([
            'finished_at' => Carbon::now(),
            'failed' => 1,
            'failed_reason' => $failedReason,
        ]);
    }

    public function completeRefundTracker()
    {
        $this->refundTracker->update([
            'finished_at' => Carbon::now(),
            'failed' => 0,
        ]);
    }

    protected function fillRefunds()
    {
        $this->outputMessage('Querying Infusionsoft for refunds...');
        foreach (InfusionsoftToken::all() as $account) {
            $this->setIsAcount($account);
            $this->activeIsAccount = $account->account;

            if (count($this->contacts)) {
                $this->setRefunds();
            }
        }
    }

    /**
     * @param $baseMessage
     */
    public function outputMessage($baseMessage)
    {
        if ($this->thrownException instanceof Exception) {
            $message = $baseMessage . PHP_EOL . $this->thrownException->getMessage();
            catch_and_return($message . PHP_EOL . $this->thrownException->getTraceAsString(), $this->thrownException);
            $this->displayMessage($message, true);
            $this->errorMessage = $baseMessage;
            $this->thrownException = null;
        } else {
            $this->displayMessage($baseMessage);
        }
    }

    /**
     * @codeCoverageIgnore
     * @param $message
     * @param bool $isError
     */
    protected function displayMessage($message, $isError = false)
    {
        if (app()->environment() === 'local' && $this->command->argument('debug')) {
            if ($isError) {
                $this->command->error($message);
            } else {
                $this->command->info($message);
            }
        }
    }

    /**
     * @param InfusionsoftToken $account
     */
    protected function setIsAcount(InfusionsoftToken $account)
    {
        $this->setDefaults();
        $this->setContacts($account->account);

        $this->isAccount = app(Infusionsoft::class, ['account' => $account->account]);
    }

    /**
     * @codeCoverageIgnore
     * @return Infusionsoft
     */
    public function getIsAccount()
    {
        return $this->isAccount;
    }

    /**
     * @param Carbon $date
     * @return Refunds
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getRefunds()
    {
        return $this->refunds;
    }

    protected function setRefunds()
    {
        $this->retrieveRefunds($this->date ? ['LastUpdated' => "~>~ {$this->date->toDateTimeString()}"] : []);
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param Command $command
     * @return Refunds
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @param Collection $refunds
     */
    protected function addToRefunds(Collection $refunds)
    {
        $this->setInvoices($refunds);
        $this->page++;
        $refunds->each(function (Collection $refund) {
            /** @var DateTime $date */
            $date = $refund->get('LastUpdated')->format("Y-m-d H:i:s");
            $this->outputMessage("Refund: {$refund->get('Id')} ({$date}) retrieved");
            $refund->put('Invoice', $this->getInvoice($refund))
                ->put('LastUpdated', $date)
                ->forget('InvoiceId');
            $this->refunds->push($refund);
        });
    }

    /**
     * @param Collection $refunds
     */
    protected function setInvoices(Collection $refunds)
    {
        $invoiceIds = collect($this->getIds($refunds->whereNotIn('Id', $this->refunds->pluck('Id'))
            ->pluck('InvoiceId')));

        /** @var Collection $chunk */
        foreach ($invoiceIds->chunk(500) as $chunk) {
            $invoices = [];

            try {
                $invoices = $this->getIsAccount()->data()->query(
                    'Invoice',
                    $this->limit,
                    0,
                    [
                        'Id' => $chunk->toArray(),
                    ],
                    [
                        'Id',
                        'InvoiceTotal',
                        'InvoiceType',
                        'PayPlanStatus',
                        'PayStatus',
                        'RefundStatus',
                        'TotalPaid',
                        'ProductSold',
                    ],
                    'Id',
                    true
                );
            } catch (Exception $e) {
                $this->handleException($e, 'There was a problem fetching the Infusionsoft invoices.');
            }

            if (count($invoices)) {
                $this->invoices = convert_to_collection($invoices);
            }
        }
    }

    /**
     * @param Collection $collection
     * @return array
     */
    protected function getIds(Collection $collection)
    {
        $ids = [];

        $collection->unique()->each(function ($data) use (&$ids) {
            array_push($ids, (int)$data);
        });

        return $ids;
    }

    /**
     * @param Collection $refund
     * @return Collection
     */
    protected function getInvoice(Collection $refund)
    {
        return $this->invoices ? $this->invoices->where('Id', $refund->get('InvoiceId'))->first() : new Collection();
    }

    protected function setDefaults()
    {
        if (!$this->refunds) {
            $this->refunds = new Collection();
        }
    }

    protected function setContacts($isAccount)
    {
        $this->contacts = [];

        $users = User::with(['infusionsoftContact'])->has('infusionsoftContact')->get();

        $users->each(function (User $user) use ($isAccount) {
            $contacts = $user->infusionsoftContact->where('is_account', $isAccount)->sortBy('is_account')
                ->pluck('is_contact_id')->unique()->map(function ($data) {
                    return (int) $data;
                })->toArray();
            $this->contacts = array_merge($contacts, $this->contacts);
            asort($this->contacts);
        });
    }

    /**
     * @param array $fromDate
     */
    protected function retrieveRefunds(array $fromDate = [])
    {
        $refunds = [];

        try {
            $refunds = $this->getIsAccount()->data()->query(
                'Payment',
                $this->limit,
                $this->page,
                array_merge($fromDate, [
                    'PayType' => 'Refund',
                    'ContactId' => array_values($this->contacts),
                ]),
                [
                    'Id',
                    'ContactId',
                    'InvoiceId',
                    'PayAmt',
                    'PayType',
                    'RefundId',
                    'LastUpdated',
                ],
                'LastUpdated',
                true
            );
        } catch (Exception $e) {
            $this->handleException($e, 'There was a problem fetching the Infusionsoft refunds.');
        }

        if (count($refunds)) {
            $this->addToRefunds(convert_to_collection($refunds));
        }

        if (count($refunds) === $this->limit) {
            /** Cannot reproduce this from a test */
            // @codeCoverageIgnoreStart
            $this->retrieveRefunds();
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @param Exception $e
     * @param $message
     */
    public function handleException(Exception $e, $message)
    {
        $this->errorMessage = $this->checkTokenExceptionErrorMessage($e, $message);
        $this->addRefundTrackerHistory('error', $this->errorMessage);

        try {
            throw new Exception($message);
        } catch (Exception $e) {
            $this->setThrownException($e);
            $this->outputMessage($message);
        }
    }

    /**
     * @param Exception $e
     * @param $message
     * @return string
     */
    protected function checkTokenExceptionErrorMessage(Exception $e, $message)
    {
        return $e instanceof TokenExpiredException ?
            $message . ' ' . "The Infusionsoft account [{$this->activeIsAccount}] token has expired" :
            $message;
    }

    /**
     * @param Exception|null $thrownException
     */
    public function setThrownException($thrownException)
    {
        $this->thrownException = $thrownException;
    }

    /**
     * @param string $identifier
     * @return Refunds
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }
}