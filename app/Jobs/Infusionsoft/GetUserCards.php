<?php
namespace App\Jobs\Infusionsoft;

use App\Models\User;
use Infusionsoft\Infusionsoft;

class GetUserCards implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type string
     */
    private $account;

    /**
     * @type User
     */
    private $user;

    /**
     *
     */
    const FIELDS = [
        'Id', 'ContactId', 'CardType', 'Last4', 'NameOnCard', 'Status', 'Email', 'ExpirationMonth', 'ExpirationYear'
    ];

    /**
     * GetUserCards constructor.
     *
     * @param User $user
     * @param $account
     */
    public function __construct(User $user, string $account)
    {
        $this->account = $account;
        $this->user = $user;
    }

    public function handle()
    {
        /** @var $is \Infusionsoft\Infusionsoft */
        $is = app(Infusionsoft::class, ['account' => $this->account]);

        try {
            $cards = $is->data()->query(
                'CreditCard',
                10,
                0,
                ['Email' => $this->user->email],
                self::FIELDS,
                'Id',
                false
            );
            $validCards = [];

            foreach ($cards as $card) {
                if ($card['Status'] <> 3) {
                    continue;
                }

                array_push($validCards, $card);
            }

            $this->response = $validCards;

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e, "Unable to get cards");
        }
    }
}
