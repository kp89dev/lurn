<?php
namespace App\Jobs\Infusionsoft;

use App\Models\User;
use Infusionsoft\Infusionsoft;

class GetUserContactId implements InfusionsoftJobInterface
{
    use ResponseHandlerTrait;

    /**
     * @type User
     */
    private $user;
    /**
     * @type string
     */
    private $account;

    /**
     * GetUserContactId constructor.
     *
     * @param User   $user
     * @param string $account
     */
    public function __construct(User $user, string $account)
    {
        $this->user = $user;
        $this->account = $account;
    }

    public function handle()
    {
        /** @type Infusionsoft $is */
        $is = app(Infusionsoft::class, ['account' => $this->account]);

        try {
            $contact = $is->contacts('xml')->findByEmail($this->user->email, ['Id']);
            if (count($contact)) {
                $this->user->infusionsoftContact()->create([
                    'is_contact_id' => $contact[0]['Id'],
                    'is_account'    => $this->account,
                ]);

                $this->response = $this->user->contactIdFor($this->account)->first();
            }

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
