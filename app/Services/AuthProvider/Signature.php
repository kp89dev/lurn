<?php
namespace App\Services\AuthProvider;

use App\Models\Source;
use App\Models\SourceToken;

class Signature
{
    /**
     * @type string
     */
    private $signature;

    /**
     * Signature constructor.
     *
     * @param string $signature
     */
    public function __construct(string $signature)
    {
        $this->signature = $signature;
    }

    /**
     * @param SourceToken $sourceToken
     *
     * @return Signature
     */
    public static function sign(SourceToken $sourceToken, Source $source)
    {
        $idpDomain = parse_url(env('APP_URL'), PHP_URL_HOST);
        $data = $idpDomain . $sourceToken->token . $source->token;

        return new self(hash_hmac('sha256', $data, $source->token));
    }

    /**
     * @param Source $source
     * @param        $randomToken
     *
     * @return bool
     */
    public function verify(Source $source, string $randomToken)
    {
        $data = $source->url . $randomToken . $source->token;
        $knowSignature = hash_hmac('sha256', $data, $source->token);
       
        return hash_equals($knowSignature, $this->signature);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->signature;
    }
}
