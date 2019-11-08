<?php
namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthProvider\SourceUrlHandler;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Closure;

class ZeroUpLabController extends Controller
{

    public function prepRequest()
    {
        $user = Auth::user();
        if ($user && $user instanceof User) {
            foreach(config('tools') as $tool){
                if ($tool['name'] == request('tool_name')){
                    $client = new Client();
                    $res = $client->request('POST', $tool['ssoUrl'], [
                        'form_params' => [
                            'auth_token' => $tool['secret'],
                            'email' => $user->email,
                        ]
                    ]);
                    $data = json_decode($res->getBody());
                    if (isset($data->login_link)){
                        $response = array('link' => $data->login_link);
                    } else {
                        $response = array('Error' => "Unknown return");
                    }    
                }
            }
        }
        if (!isset($response)){
            $response = array('Error' => 'No User');
        }
        return response()->json($response);
    }
}
    