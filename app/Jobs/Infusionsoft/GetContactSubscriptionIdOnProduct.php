<?php
namespace App\Jobs\Infusionsoft;

use App\Models\InfusionsoftContact;
use Infusionsoft\Infusionsoft;

class GetContactSubscriptionIdOnProduct implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type InfusionsoftContact
     */
    private $contact;

    /**
     * @type int
     */
    private $productId;

    const FIELDS = [
        'Id', 'ProductId', 'Status'
    ];

    public function __construct(InfusionsoftContact $contact, int $productId)
    {
        $this->contact   = $contact;
        $this->productId = $productId;
    }

    public function handle()
    {
        /** @var $is \Infusionsoft\Infusionsoft */
        $is = app(Infusionsoft::class, ['account' => $this->contact->is_account]);

        try {
            $this->response = $is->data()->query(
                'RecurringOrder', //table
                10, //per page
                0, //page
                ['ProductId' => $this->productId, 'ContactId' => $this->contact->is_contact_id, 'Status' => 'Active'], // filter
                self::FIELDS, //fields to get
                'Id', // order by
                false // ascending
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
