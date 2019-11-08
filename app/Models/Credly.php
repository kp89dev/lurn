<?php

namespace App\Models;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Credly extends Model
{
    protected $secret, $key;
    protected $headers, $auth;
    protected $base = "https://api.credly.com";
    protected $expires, $giveable, $token;
    
    public function __construct() 
    {
        $this->secret = env('CREDLY_SECRET', null);
        $this->key = env('CREDLY_KEY', null);
        $this->user = env('CREDLY_USER', null);
        $this->pw = env('CREDLY_USER_PW', null);
        
        if($this->secret === null || $this->key === null || $this->user === null || $this->pw === null) {
            throw new \Exception('Credly credentials must be set in .env');
        }
        
        $this->headers = [
            'X-Api-Key'     => $this->key,
            'X-Api-Secret'  => $this->secret,
        ];
        
        
        
        $this->auth = [
            $this->user,
            $this->pw
        ];
        
        $this->expires = 10 * 365 * 24 * 60 * 60; //ten years
        $this->giveable = 0;
        
        $this->auth();
    }
    
    
    public function saveBadge(Badge $badge)
    {
        if($badge->credly_id) {
            $endpoint = "v1.1/badges/$badge->credly_id";
        } else {
            $endpoint = "v1.1/badges";
        }
        $method = "POST";
        
        $image = $this->base64_encode_badge($badge);
        
        $client = new Client([
            'base_uri' => $this->base
        ]);
        
        $body = [
            'attachment'        => $image,
            'title'             => $badge->title,
            'description'       => $badge->description,
            'is_givable'        => $this->giveable,
            'expires_in'        => $this->expires,
            'is_claimable'      => 1,
            'require_claim_code'            => 1,
            'approve_claim_automatically'   => 1,
            'require_claim_evidence'        => 0,
        ];
        
        $response = $client->request($method, $endpoint, 
            [
                'headers' => $this->headers,
                'auth' => $this->auth,
                'form_params'  => $body,
                'query' => ['access_token' => $this->token]
            ]
        );
        
        $data = json_decode($response->getBody());
        
        if($data->meta->status_code != 200) {
            Log::error('Badge addition to Credly has failed.');
        } else {
            $badge->credly_id = $data->data;
        }
        
        return $badge;
        
    }
    
    public function giveBadge($user_id, $badge_id)
    {
        $endpoint = "v1.1/member_badges";
        $method = "POST";
        
        $user = User::find($user_id);
        $badge = Badge::find($badge_id);
        
        if(! $badge->credly_id) {
            return;
        }
        
        $nameparts = explode(' ', $user->name, 2);
        $fname = $nameparts[0];
        $lname = isset($nameparts[1])? $nameparts[1] : 'None';
        
        $body = [
            'headers' => $this->headers,
            'auth' => $this->auth,
            'email'         => $user->email,
            'badge_id'      => $badge->credly_id,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'notify'        => 1,
        ];
        
        $client = new Client([
            'base_uri' => $this->base
        ]);
        
        $response = $client->request($method, $endpoint,
            [
                'headers' => $this->headers,
                'auth' => $this->auth,
                'form_params'  => $body,
                'query' => ['access_token' => $this->token]
            ]
            );
        
        $data = json_decode($response->getBody());
        
        if($data->meta->status_code != 200) {
            Log::error('Badge addition to Credly has failed.');
        } else {
            $badge->credly_id = $data->data;
        }
        
        return $badge;
    }
        
    protected function base64_encode_badge(Badge $badge)
    {
        return base64_encode(Storage::drive('static')->get($badge->image));
    }
    
    protected function auth()
    {
        $method = "POST";
        $endpoint = "v1.1/authenticate";
        
        $client = new Client([
            'base_uri' => $this->base
        ]);
        
        $response = $client->request($method, $endpoint, [
            'headers'   => $this->headers,
            'auth'      => $this->auth,
        ]);
        
        $data = json_decode($response->getBody());
        
        if($data->meta->status_code != 200) {
            Log::error('Authentication to Credly has failed.');
        } else {
            $this->token = $data->data->token;
        }
    }
}
