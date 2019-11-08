<?php
namespace App\Jobs\Infusionsoft;

use App\Models\User;
use Infusionsoft\Infusionsoft;

class CreateUserContact implements InfusionsoftJobInterface
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
            $name  = explode(' ', $this->user->name);

            $FirstName = $name[0];
            $LastName  = isset($name[1]) ? $name[1] : '';
            $Email = $this->user->email;

            $contact = $is->contacts('xml')->add(compact('FirstName', 'LastName', 'Email'));

            $this->user->infusionsoftContact()->create([
                'is_contact_id' => $contact,
                'is_account'    => $this->account
            ]);

            $this->response = $this->user->contactIdFor($this->account)->first();

            return $this;
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
