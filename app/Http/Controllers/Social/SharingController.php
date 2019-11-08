<?php

namespace App\Http\Controllers\Social;

use App\Http\Controllers\Controller;

class SharingController extends Controller
{
    /**
     *
     * @var string
     */
    protected $service;

    /**
     *
     * @var string
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $secret;

    /**
     *
     * @var string
     */
    protected $id;

    public function __construct()
    {
        switch ($this->service) {
            case 'twitter':
                if (request()->hasCookie('twitter_auth')) {
                    $crumbs = explode('|', request()->cookie('twitter_auth'));
                    $this->client = $crumbs[0];
                    $this->secret = $crumbs[1];
                }
                break;

            case 'facebook':
                if (request()->hasCookie('facebook_auth')) {
                    $this->client = request()->cookie('facebook_auth');
                }
                break;
        }
    }
}