<?php

namespace App\Http\Controllers\Social;

use App\Events\Social\FacebookPostShared;
use Illuminate\Http\Request;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Facebook;

class FacebookController extends SharingController
{
    /**
     * Create a new instance of the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = 'facebook';

        parent::__construct();
    }

    /**
     * Show the sharing form.
     *
     * @return View
     */
    public function index()
    {
        return view('social.facebook');
    }

    /**
     * Share on social media.
     * 
     * @return View
     */
    public function share(Request $request, LaravelFacebookSdk $fb)
    {
        $fb->setDefaultAccessToken($this->client);

        $post = $fb->post('me/feed', ['message' => $request->message]);

        $body = $post->getDecodedBody();

        event(new FacebookPostShared(user(), $body['id']));
        
        return redirect()->route('profile')->with('alert-success', 'Message shared!');
    }
}