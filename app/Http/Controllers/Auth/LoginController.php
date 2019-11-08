<?php

namespace App\Http\Controllers\Auth;

use App\Events\User\ImportedUserMerged;
usE App\Events\User\UserCreatedThroughExternalFunnel;
use App\Http\Controllers\Controller;
use App\Models\ImportedUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Events\User\ImportedUserLoggedIn;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as traitLogin;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        try {
            $response = $this->traitLogin($request);
        } catch (ValidationException $e) {}

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->guard()->check()) {
            return $this->sendMainUserLoginResponse($request, $response);
        }

        // Check if user exists in users table (was already imported).
        if ($this->wasUserImported($request)) {
            return $this->sendFailedLoginResponse($request);
        }

        return $this->attemptLoginAgainstImportTable($request);
    }
    
    /**
     * Logs the user out. If the user is an admin impersonator, logs that user back in as admin.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $admin_id = $request->session()->pull('admin_impersonator', null);
        
        if($admin_id) {
            $user = Auth::user();
            
            $admin_id = decrypt($admin_id);
            $admin = User::findOrFail($admin_id);
            
            Auth::login($admin);
            
            return redirect()->route('users.show', ['user' => $user->id]);
        }
        
        Auth::logout();
        return redirect()->to('/');
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function attemptLoginAgainstImportTable(Request $request) : \Illuminate\Http\RedirectResponse
    {
        $importedUsers = ImportedUser::byEmail(trim($request->email))->get();

        if (! count($importedUsers)) {
            return $this->sendFailedLoginResponse($request);
        }

        $success = false;
        
        foreach ($importedUsers as $importedUser) {
            if ($importedUser->isMerged()) {
                return $this->sendMergedUserLoginFailedResponse($request, $importedUser);
            }

            $user = $this->testPasswordAgainsImportTable($request, $importedUser);

            if ($user && $this->attemptLogin($request)) {
                event(new ImportedUserLoggedIn($importedUser, $user));
                $success = true;
            }
        }
        
        return $success
            ? $this->sendLoginResponse($request)
            : $this->sendFailedLoginResponse($request);
    }

    /**
     * @param Request      $request
     * @param ImportedUser $importedUser
     *
     * @return mixed
     */
    private function createUserFromImported(Request $request, ImportedUser $importedUser) : User
    {
        $user = User::where('email', '=', $importedUser->email)->first();
        
        if(!$user) {
            /** @type User $user */
            $user = User::create([
                'email'    => $importedUser->email,
                'password' => bcrypt($request->password),
                'name'     => $importedUser->name,
                'status'   => 'confirmed',
            ]);
        }
        $user->mergedImportedAccounts()
             ->attach($importedUser, ['from_table' => 'users_import_all']);

        event(new ImportedUserMerged($user, $importedUser));

        return $user;
    }

    /**
     * @param Request      $request
     * @param ImportedUser $importedUser
     *
     * @return bool
     */
    private function validMd5Password(Request $request, ImportedUser $importedUser) : bool
    {
        return strcmp(md5($request->password . $importedUser['salt']), $importedUser['md5password']) === 0;
    }

    /**
     * @param Request      $request
     * @param ImportedUser $importedUser
     *
     * @return bool
     */
    private function validBcryptPassword(Request $request, ImportedUser $importedUser) : bool
    {
        return password_verify($request->password, $importedUser['password']);
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function wasUserImported(Request $request) : bool
    {
        return (bool) User::byEmail($request->email)->first();
    }

    /**
     * @param Request $request
     * @param         $importedUser
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function sendMergedUserLoginFailedResponse(Request $request, $importedUser)
    {
        return $this->sendFailedLoginResponse($request)
            ->withErrors([
                'email' => 'Login denied with ' . $importedUser->email
                    . '. Please use ' . $importedUser->mainUser->first()->email . ' instead.'
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     */
    private function sendUnconfirmedLoginFailedResponse(Request $request)
    {
        return $this->sendFailedLoginResponse($request)
            ->withErrors([
                'email' => "This account's emaill address has not been confirmed yet."
                        . " Please check your email for the confirmation link."
                        . " If you didn't receive the email, check the Junk or"
                        . ' Spam folders or click "Resend verification" below.'
            ]);
    }

    /**
     * @param Request $request
     * @param         $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function sendMainUserLoginResponse(Request $request, $response)
    {
        $user = $this->guard()->user();

        if ($user->isMerged()) {
            $this->guard()->logout();

            return $this->sendMergedUserLoginFailedResponse($request, $user);
        }

        if ($user->statusName == 'unconfirmed') {
            $this->guard()->logout();

            return $this->sendUnconfirmedLoginFailedResponse($request);
        }

        // @todo This is a temporary event for Lurn10x users.
        // This can be removed eventually. Maybe a month or two
        // after launch?
        event(new UserCreatedThroughExternalFunnel($user, 'lurn10x'));

        return $response;
    }

    /**
     * @param Request $request
     * @param         $importedUser
     *
     * @return User|mixed
     */
    private function testPasswordAgainsImportTable(Request $request, $importedUser)
    {
        if ($this->validBcryptPassword($request, $importedUser)) {
            return $this->createUserFromImported($request, $importedUser);
        }

        if ($this->validMd5Password($request, $importedUser)) {
            return $this->createUserFromImported($request, $importedUser);
        }

        return null;
    }
}
