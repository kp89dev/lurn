<?php
namespace App\Services\AuthProvider;

use App\Models\Source;
use App\Models\SourceToken;
use App\Models\User;

class SourceUrlHandler
{
    public function getLoginUrl(User $user, Source $source)
    {
        $token     = SourceToken::createNew($source, $user);
        $signature = Signature::sign($token, $source);

        return ($source->secure ? 'https://' : 'http://') .
        $source->url .
        '/lurn/login' .
        '?t=' . $this->urlSafeEncode($token->token) .
        '&s=' . $this->urlSafeEncode($signature);
    }

    protected function urlSafeEncode(string $signature)
    {
        $encoded = base64_encode($signature);

        return str_replace(
            ['+', '=', '/'],
            ['-', '_', '~'],
            $encoded
        );
    }

    public function urlSafeDecode($signatureToken)
    {
        $encoded = str_replace(
            ['-', '_', '~'],
            ['+', '=', '/'],
            $signatureToken
        );

        return base64_decode($encoded);
    }
}
