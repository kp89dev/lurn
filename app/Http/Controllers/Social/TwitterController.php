<?php

namespace App\Http\Controllers\Social;

use App\Events\Social\TwitterPostShared;
use Illuminate\Http\Request;
use Twitter;

class TwitterController extends SharingController
{
    /**
     * Create a new instance of the controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = 'twitter';

        parent::__construct();
    }

    /**
     * Show the sharing form.
     *
     * @return View
     */
    public function index()
    {
        return view('social.twitter', ['client' => $this->client, 'secret' => $this->secret]);
    }

    /**
     * Share on social media.
     * 
     * @return View
     */
    public function share(Request $request)
    {
        Twitter::reconfig([
            'consumer_key'    => env('TWITTER_CONSUMER_KEY'),
            'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
            'token'           => $this->client,
            'secret'          => $this->secret
        ]);

        $data = Twitter::postTweet([
            'status' => $request->message,
            'format' => 'json'
        ]);

        $tweet = json_decode($data);

        event(new TwitterPostShared(user(), $tweet->id_str));

        return redirect()->route('profile')->with('alert-success', 'Message shared!');
    }
}