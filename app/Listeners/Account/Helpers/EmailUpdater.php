<?php


namespace App\Listeners\Account\Helpers;


use App\Models\Source;
use GuzzleHttp\Client;

trait EmailUpdater
{
    /**
     * @param $oldEmail
     * @param $newEmail
     * @param $url
     */
    public function sendEmailUpdateRequest($oldEmail, $newEmail, $url)
    {
        $client = app()->make(\GuzzleHttp\Client::class);

        $client->post(
            $url, [
                'form_params' => [
                    'secret'     => $this->getSecretFor($url),
                    'oldEmail'   => $oldEmail,
                    'newEmail'   => $newEmail
                ]
            ]
        );
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    private function getSecretFor($url)
    {
        $parsed  = parse_url($url);
        $source  = Source::where('url', '=', $parsed['host'])->first();

        return $source->token;
    }
}
