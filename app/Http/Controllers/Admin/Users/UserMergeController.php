<?php
namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\ImportedUser;
use App\Models\User;
use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserMergeController extends Controller
{
    public function index(Request $request)
    {
        $mainUser = User::find($request->main_user['id']);

        $userToMerge = $this->getUserToMergeFromRequest($request);
        if (null === $userToMerge) {
            return response()->json(['message' => 'Failed! Unable to find user to merge'], 412);
        }

        if ($this->isUserAlreadyMerged($mainUser, $request)) {
            return response()->json(['message' => 'Failed! User was already merged'], 412);
        }

        $this->mergeUsers($mainUser, $userToMerge, $request);
        return response()->json(['message' => 'User merged successfully']);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function getUserToMergeFromRequest(Request $request)
    {
        if (isset($request->user_to_merge['user_id'])) {
            return ImportedUser::whereUserId($request->user_to_merge['user_id'])->first();
        }

        return  User::find($request->user_to_merge['id']);
    }

    /**
     * @param User    $mainUser
     * @param Request $request
     *
     * @return bool
     */
    private function isUserAlreadyMerged(User $mainUser, Request $request) : bool
    {
        if (isset($request->user_to_merge['an_id'])) {
            return null !== $mainUser->getMergedImportedAccountByImportTableId($request->user_to_merge['an_id'])
                                     ->first();
        }
        return null !== $mainUser->getMergedAccountByUserId($request->user_to_merge['id'])
                                 ->first();
    }

    /**
     * @param User    $mainUser
     * @param Model   $userToMerge
     * @param Request $request
     */
    private function mergeUsers(User $mainUser, Model $userToMerge, Request $request)
    {
        if (isset($request->user_to_merge['user_id'])) {
            $mainUser->mergedImportedAccounts()
                     ->attach($userToMerge, ['from_table' => 'users_import_all']);

            event(new ImportedUserMerged($mainUser, $userToMerge));

            return;
        }

        $mainUser->mergedAccounts()
                 ->attach($userToMerge, ['from_table' => 'users']);
        event(new UserMerged($mainUser, $userToMerge));
    }
}
