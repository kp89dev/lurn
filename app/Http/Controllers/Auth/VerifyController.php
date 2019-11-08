<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendVerificationRequest;
use App\Models\User;
use App\Notifications\VerificationEmail;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;

class VerifyController extends Controller
{
    /**
     * VerifyController constructor.
     */
    public function __construct()
    {
        $this->middleware(function ($request, Closure $next) {
            if (user()) {
                return redirect()->route('dashboard');
            }

            return $next($request);
        });
    }

    /**
     * @param $encryptedId
     * @return mixed
     */
    public function index($encryptedId)
    {
        $user = $this->getUserFromEncryptedId($encryptedId);

        if ($user->statusName == 'unconfirmed') {
            $user->update(['status' => 'confirmed']);

            // Log the user in only if the status was previously unconfirmed.
            // This way we cancel the confirmation link and if somebody else
            // finds it later, it doesn't get logged in as the original user.
            Auth::loginUsingId($user->id, true);
        }

        return redirect()
            ->route('dashboard')
            ->withSuccess('Your account has been successfully verified!');
    }

    private function getUserFromEncryptedId($encryptedId)
    {
        try {
            $id = Crypt::decrypt($encryptedId);
        } catch (DecryptException $e) {
            abort(404);
        }

        if (! $user = User::find($id)) {
            abort(404);
        }

        return $user;
    }

    /**
     * @return View
     */
    public function showResendForm()
    {
        return view('auth.resend-verification');
    }

    /**
     * @param ResendVerificationRequest $request
     * @return mixed
     */
    public function resend(ResendVerificationRequest $request)
    {
        $user = User::whereEmail($request->email)->first();

        $user->notify(new VerificationEmail);

        return redirect()->back()
            ->withSuccess("We've sent you another confirmation email. Please check the Junk or Spam folders, too.");
    }
}
