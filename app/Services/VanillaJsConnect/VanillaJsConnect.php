<?php
namespace App\Services\VanillaJsConnect;

class VanillaJsConnect
{

    private $jsConnectVersion = '2';
    private $jsTimeout = 1440; // 24 * 60

    public function writeJsConnect($user, $clientID, $secret, $secure = true)
    {
        $request = request()->all();

        $user = array_change_key_case($user);

        if ($secure) {
            if (!isset($request['v'])) {
                $error = array('error' => 'invalid_request', 'message' => 'Missing the v parameter.');
            } elseif ($request['v'] !== $this->jsConnectVersion) {
                $error = array('error' => 'invalid_request', 'message' => "Unsupported version {$request['v']}.");
            } elseif (!isset($request['client_id'])) {
                $error = array('error' => 'invalid_request', 'message' => 'Missing the client_id parameter.');
            } elseif ($request['client_id'] != $clientID) {
                $error = array('error' => 'invalid_client', 'message' => "Unknown client {$request['client_id']}.");
            } elseif (!isset($request['timestamp']) && !isset($request['sig'])) {
                if (is_array($user) && count($user) > 0) {
                    $error = array('name' => (string) @$user['name'], 'photourl' => @$user['photourl'], 'signedin' => true);
                } else {
                    $error = array('name' => '', 'photourl' => '');
                }
            } elseif (!isset($request['timestamp']) || !ctype_digit($request['timestamp'])) {
                $error = array('error' => 'invalid_request', 'message' => 'The timestamp parameter is missing or invalid.');
            } elseif (!isset($request['sig'])) {
                $error = array('error' => 'invalid_request', 'message' => 'Missing the sig parameter.');
            } elseif (abs($request['timestamp'] - $this->JsTimestamp()) > $this->jsTimeout) {
                $error = array('error' => 'invalid_request', 'message' => 'The timestamp is invalid.');
            } elseif (!isset($request['nonce'])) {
                $error = array('error' => 'invalid_request', 'message' => 'Missing the nonce parameter.');
            } elseif (!isset($request['ip'])) {
                $error = array('error' => 'invalid_request', 'message' => 'Missing the ip parameter.');
            } else {
                $signature = $this->jsHash($request['ip'] . $request['nonce'] . $request['timestamp'] . $secret, $secure);
                if ($signature != $request['sig']) {
                    $error = array('error' => 'access_denied', 'message' => 'Signature invalid.');
                }
            }
        }

        if (isset($error)) {
            $result = $error;
        } elseif (is_array($user) && count($user) > 0) {
            if ($secure === null) {
                $result = $user;
            } else {
                $result = $this->signJsConnect($user, $clientID, $secret, $secure, true);
                $result['v'] = $this->jsConnectVersion;
            }
        } else {
            $result = array('name' => '', 'photourl' => '');
        }
      
        if (isset($request['callback'])) {
            return response()->json($result)
                ->withCallback($request['callback'])
                ->header('Content-Type', 'application/javascript');
        } else {
            return response()->json($result);
        }
    }

    private function signJsConnect($data, $clientID, $secret, $hashType, $returnData = false)
    {
        $normalizedData = array_change_key_case($data);
        ksort($normalizedData);

        foreach ($normalizedData as $key => $value) {
            if ($value === null) {
                $normalizedData[$key] = '';
            }
        }

        $stringifiedData = http_build_query($normalizedData, null, '&');
        $signature = $this->jsHash($stringifiedData . $secret, $hashType);
        if ($returnData) {
            $normalizedData['client_id'] = $clientID;
            $normalizedData['sig'] = $signature;
            return $normalizedData;
        } else {
            return $signature;
        }
    }

    private function jsHash($string, $secure = true)
    {
        if ($secure === true) {
            $secure = 'md5';
        }

        switch ($secure) {
            case 'sha1':
                return sha1($string);
            case 'md5':
            case false:
                return md5($string);
            default:
                return hash($secure, $string);
        }
    }

    private function jsTimestamp()
    {
        return time();
    }
}
