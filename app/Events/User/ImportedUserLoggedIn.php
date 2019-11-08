<?php
namespace App\Events\User;

use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class ImportedUserLoggedIn
{
    use SerializesModels;

    /**
     * @type ImportedUser
     */
    public $importedUser;


    /**
     * @type User
     */
    public $user;

    /**
     * ImportedUserLoggedIn constructor.
     *
     * @param ImportedUser $importedUser
     * @param User $user
     */
    public function __construct(ImportedUser $importedUser, User $user)
    {
        $this->importedUser = $importedUser;
        $this->user = $user;
    }

}
