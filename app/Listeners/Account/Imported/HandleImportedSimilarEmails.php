<?php
namespace App\Listeners\Account\Imported;

use App\Events\User\ImportedUserLoggedIn;
use App\Events\User\ImportedUserMerged;
use App\Models\ImportedUser;

class HandleImportedSimilarEmails
{
    public function handle(ImportedUserLoggedIn $userLoggedIn)
    {
        $users = ImportedUser::whereEmail($userLoggedIn->importedUser->email)->get();
        

        foreach ($users as $user) {
            if ($user->email == $userLoggedIn->user->email) {
                continue;
            }

            $userLoggedIn->user
                         ->mergedImportedAccounts()
                         ->attach($user, ['from_table' => 'users_import_all']);

            event(new ImportedUserMerged($userLoggedIn->user, $user));
        }
    }
}
