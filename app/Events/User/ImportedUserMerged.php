<?php
namespace App\Events\User;

use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class ImportedUserMerged
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

    public function __construct(User $user, ImportedUser $importedUser)
    {
        $this->importedUser = $importedUser;
        $this->user = $user;
    }
}
