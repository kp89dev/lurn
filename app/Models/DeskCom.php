<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use GuzzleHttp\Client;
use App\Models\User;

class DeskCom extends Model
{
    protected $deskuser;
    protected $deskpassword;
    protected $client;
    protected $headers;
    protected $base;
    protected $method;
    
    public function __construct()
    {
        $this->deskuser = env('DESKCOM_USER', null);
        $this->deskpassword = env('DESKCOM_PW', null);
        
        if((null === $this->deskuser || null === $this->deskpassword) && App::environment(['staging', 'production'])) {
            throw new \Exception('Desk.com credentials are not set. Please check the configuarion.');
        }
        
        $this->headers = ['Accept' => 'application/json'];
        $this->base = 'https://vssmind.desk.com/api/v2/';
        $this->method = 'GET';
        
    }
    
    public function getCaseHistory(User $user)
    {
        $emails = [];
        
        $emails[] = $user->email;
        
        foreach($user->mergedAccounts as $import) {
            $emails[] = $import->email;
        }
        
        $emails = array_unique($emails);
        
        $email_string = trim(implode(',', $emails), ',');
        
        $client = new Client([
            'base_uri' => $this->base
        ]);
        
        $response = $client->request($this->method, 'cases',
        [
            'headers'   => $this->headers,
            'auth'      => $this->auth(),
            'query'     => [
                'email'     => $email_string,
                'embed'     => 'messages',
                'fields'    => 'description,name,subject,status,blurb,active_at,id'
            ]
        ]);
        
        if($response->getStatusCode() != 200) {
            throw new \Exception('Desk.com API Unavailable');
        }
        
        $data = json_decode($response->getBody());
        if(count($data->_embedded->entries)) {
            foreach($data->_embedded->entries as $key=>$entry) {
                $data->_embedded->entries[$key]->active_at = [
                    'carbon' => \Carbon\Carbon::createFromTimeStamp(strtotime($entry->active_at)),
                    'org'   => $entry->active_at
                ];
            }
        }
        
        return $data;
    }
    
    protected function auth()
    {
        return [$this->deskuser, $this->deskpassword];
    }
}
