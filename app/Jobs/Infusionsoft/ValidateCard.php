<?php
namespace App\Jobs\Infusionsoft;

use App\Models\InfusionsoftContact;
use Infusionsoft\Infusionsoft;

class ValidateCard implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type InfusionsoftContact
     */
    private $contact;
    /**
     * @type \stdClass
     */
    private $card;

    /**
     * ValidateCard constructor.
     *
     * @param InfusionsoftContact $contact
     * @param \stdClass           $card
     */
    public function __construct($contact, \stdClass $card)
    {
        $this->contact = $contact;
        $this->card = $card;
    }

    public function handle()
    {
        /** @var $is \Infusionsoft\Infusionsoft */
        $is = app(Infusionsoft::class, ['account' => $this->contact->is_account]);

        try {
            $this->response = $is->invoices()->validateCreditCard(
                $this->card->type,
                $this->contact->is_contact_id,
                $this->card->number,
                $this->card->expMonth,
                $this->card->expYear,
                $this->card->cvv
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleResponse()
    {
        if ($this->response['Valid'] !== "true") {
            $this->error = $this->response["Message"];
        }
    }
}
