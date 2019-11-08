<?php
namespace App\Jobs\Infusionsoft;

use App\Models\InfusionsoftContact;
use Infusionsoft\Infusionsoft;

class CancelSubscription implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type InfusionsoftContact
     */
    private $contact;
    /**
     * @type int
     */
    private $subscriptionId;

    public function __construct(InfusionsoftContact $contact, int $subscriptionId)
    {
        $this->contact        = $contact;
        $this->subscriptionId = $subscriptionId;
    }

    public function handle()
    {
        /** @var $is \Infusionsoft\Infusionsoft */
        $is = app(Infusionsoft::class, ['account' => $this->contact->is_account]);


        try {
            $this->response = $is->data()->update(
                'RecurringOrder',
                $this->subscriptionId,
                [
                    'Status'        => 'Inactive',
                    'ReasonStopped' => 'Cancelled Through Lurn Interface'
                ]
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
