<?php
namespace App\Jobs\Infusionsoft;

use App\Models\InfusionsoftContact;
use Infusionsoft\Infusionsoft;

class AddContactCard implements InfusionsoftJobInterface
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
            $this->response =  $is->data()->add(
                'CreditCard',
                [
                    'ContactId'       => $this->contact->is_contact_id,
                    'Email'           => $this->contact->user->email,
                    'CardNumber'      => $this->card->number,
                    'CVV2'            => $this->card->cvv,
                    'ExpirationMonth' => $this->card->expMonth,
                    'ExpirationYear'  => $this->formatExpYear(),
                    'NameOnCard'      => $this->card->nameOnCard,
                    'CardType'        => $this->card->type
                ]
            );

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * @return int
     */
    private function formatExpYear()
    {
        return strlen($this->card->expYear) == 2 ? (2000 + $this->card->expYear) : $this->card->expYear;
    }
}
