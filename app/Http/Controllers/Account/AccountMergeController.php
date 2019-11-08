<?php
namespace App\Http\Controllers\Account;

use App\Events\User\ImportedUserMerged;
use App\Events\User\UserMerged;
use App\Http\Controllers\Controller;
use App\Models\AccountMergeToken;
use App\Models\ImportedUser;
use App\Models\User;
use App\Notifications\Account\AccountMergeConfirmation;
use Illuminate\Http\Request;
use Closure;

class AccountMergeController extends Controller
{
        public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            $this->seoSetup($request);

            return $next($request);
        });
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('account.account-merge');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        if ($request->email == $request->user()->email) {
            return response()->json(['data' => []]);
        }

        $user = $this->getUserByEmail($request);

        return response()->json(['data' => $user]);
    }

    /**
     * @param Request                 $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function initiateMerge(Request $request)
    {
        $user =  $this->getUserByEmail($request);

        if (! $user->count()) {
            return response()->json([
                'data' => null,
                'error' => 'Account to be merged not found'
            ]);
        }

        /**@var $anAccount User */
        $anAccount = $user->first();

        //create a new token
        $tokenModel = new AccountMergeToken();
        $token = $tokenModel->getNewToken($request->user(), $anAccount);

        //send the email
        $anAccount->notify(new AccountMergeConfirmation($token));

        return response()->json([
            'data'    => null,
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     */
    public function proceedMerge(Request $request)
    {
        $token = AccountMergeToken::whereEmailOwner($request->user()->email)->first();

        if (is_null($token)) {
            return redirect(route('account-merge.index'))
                        ->withErrors('Unable to do the merge. Invalid link ' .
                            'provided or you are logged in with the wrong account.');
        }

        if (! $token->valid($request->token)) {
            return redirect(route('account-merge.index'))
                        ->withErrors('The link for merging accounts expired. Please try again.');
        }

        $this->mergeAccounts($request->user(), $token->email_to_merge);
        
        return redirect(route('account-merge.index'))->with('message', 'Account merged succesfully');
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    private function getUserByEmail(Request $request)
    {
        $user = User::whereEmail($request->email)->get();

        if (!$user->count()) {
            $user = ImportedUser::whereEmail($request->email)->get();

            return $user;
        }

        return $user;
    }

    /**
     * @param User $owner
     * @param string $email
     */
    private function mergeAccounts(User $owner, string $email)
    {
        $importedUsers = ImportedUser::whereEmail($email)->get();
        foreach ($importedUsers as $iUser) {
            $owner->mergedImportedAccounts()
                  ->attach($iUser, ['from_table' => 'users_import_all']);

            event(new ImportedUserMerged($owner, $iUser));
        }

        $users = User::whereEmail($email)->get();

        foreach ($users as $nUser) {
            //ignore the owner
            if ($nUser->id == $owner->id) {
                continue;
            }

            $owner->mergedAccounts()
                ->attach($nUser, ['from_table' => 'users']);

            event(new UserMerged($owner, $nUser));
        }
    }
}
