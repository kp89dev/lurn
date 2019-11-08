<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Controller;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Socialite;

class LoginController extends Controller
{
    /**
     * Redirect the user to the twitter authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($service, LaravelFacebookSdk $fb)
    {
        switch ($service) {
            case 'twitter':
                return Socialite::driver($service)->redirect();
            case 'facebook':
                return redirect()->away($fb->getLoginUrl());
        }
    }

    /**
     * Obtain the user information from twitter.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($service, LaravelFacebookSdk $fb)
    {
        switch ($service) {
            case 'twitter':
                return $this->handleTwitterLogin();
            case 'facebook':
                return $this->handleFacebookLogin($fb);
        }
    }

    /**
     * Handle logins through Twitter OAuth2.
     *
     * @return RedirectResponse
     */
    public function handleTwitterLogin()
    {
        $user   = Socialite::driver('twitter')->user();

        // The user denied the request or we just landed on this URL.
        if (! $user) {
            return redirect()->route('profile');
        }

        $token  = $user->accessTokenResponseBody['oauth_token'];
        $secret = $user->accessTokenResponseBody['oauth_token_secret'];
        $cookie = cookie('twitter_auth', "{$token}|{$secret}", 7200);

        return redirect()->route('social-share.twitter')->withCookie($cookie);
    }

    /**
     * Handle logins through Facebook OAuth2.
     *
     * @param  LaravelFacebookSdk $fb
     * @return RedirectResponse
     */
    public function handleFacebookLogin(LaravelFacebookSdk $fb)
    {
        try {
            $token = $fb->getAccessTokenFromRedirect();
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            return redirect()->route('profile')->with('errors', $e->getMessage());
        }

        // The user denied the request or we just landed on this URL.
        if (! $token) {
            return redirect()->route('profile');
        }

        // Extend access token.
        if (! $token->isLongLived()) {
            $client = $fb->getOAuth2Client();

            try {
                $token = $client->getLongLivedAccessToken($token);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                return redirect()->route('profile')->with('errors', $e->getMessage());
            }
        }

        $fb->setDefaultAccessToken($token);

        $cookie = cookie('facebook_auth', (string) $token, 7200);

        return redirect()->route('social-share.facebook')->withCookie($cookie);
    }
}