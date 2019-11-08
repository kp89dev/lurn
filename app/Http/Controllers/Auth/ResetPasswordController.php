<?php

namespace App\Http\Controllers\Auth;

use App\Events\User\ImportedUserLoggedIn;
use App\Http\Controllers\Controller;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        reset as resetTrait;
    }

    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $response = $this->resetTrait($request);

        if (! $response->getSession()->has('errors')) {
            return $response;
        }

        Password::setDefaultDriver('users_imported');

        $importedResetReponse = $this->broker()->reset(
                $this->credentials($request),
                function (ImportedUser $user, $password) {
                    $mainUser = new User([
                        'email'    => $user->email,
                        'name'     => $user->name
                    ]);
                    $this->resetPassword($mainUser, $password);
                    $mainUser->mergedImportedAccounts()
                             ->attach($user, ['from_table' => 'users_import_all']);
                    
                    event(new ImportedUserLoggedIn($user, $mainUser));
                }
        );

        if ($importedResetReponse == Password::PASSWORD_RESET) {
            $response->getSession()->remove('errors');
            return $this->sendResetResponse($importedResetReponse);
        }

        return $response;
    }
}
