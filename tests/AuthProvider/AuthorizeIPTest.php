<?php
namespace Tests\AuthProvider;

use App\Models\Source;

class AuthorizeIPTest extends \TestCase
{

    /**
     * @test
     */
    public function login_idp_no_vars_unauth_test()
    {
        $response = $this->get(route('idp.login'));

        $response->assertStatus(401)
            ->assertSee('Unauthorized.');
    }

    /**
     * @test
     */
    public function login_idp_random_vars_unauth_test()
    {

        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $token = bin2hex(random_bytes(50));
        } else {
            $randomStringFactory = new \RandomLib\Factory();
            $generator = $randomStringFactory->getMediumStrengthGenerator();
            $token = $generator->generate(50);
        }

        $parameters = [
            'source' => 'somesource.com',
            's' => '1234',
            't' => $token
        ];

        $response = $this->get(route('idp.login', $parameters));

        $response->assertStatus(401)
            ->assertSee('Unauthorized.');
    }

    /**
     * @test
     */
    public function login_idp_with_source_unauth_test()
    {

        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $token = bin2hex(random_bytes(50));
        } else {
            $randomStringFactory = new \RandomLib\Factory();
            $generator = $randomStringFactory->getMediumStrengthGenerator();
            $token = $generator->generate(50);
        }

        $source = factory(Source::class)->create();

        $parameters = [
            'source' => $source->url,
            's' => '1234',
            't' => $token
        ];

        $response = $this->get(route('idp.login', $parameters));

        $response->assertStatus(401)
            ->assertSee('Unauthorized.');
    }

    /**
     * @test
     */
    public function login_idp_request_invalid_signature_unauth_test()
    {
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $token = bin2hex(random_bytes(50));
        } else {
            $randomStringFactory = new \RandomLib\Factory();
            $generator = $randomStringFactory->getMediumStrengthGenerator();
            $token = $generator->generate(50);
        }

        $source = factory(Source::class)->create();
        $siteToken = $source->token;
        $data = $source . $token . $siteToken;
        $signature = hash_hmac('sha256', $data, $siteToken);

        $parameters = [
            'source' => $source->url,
            's' => str_replace(['+', '=', '/'], ['-', '_', '~'], $signature),
            't' => str_replace(['+', '=', '/'], ['-', '_', '~'], $token)
        ];

        $response = $this->get(route('idp.login', $parameters));

        $response->assertStatus(401)
            ->assertSee('Unauthorized.');
    }

    /**
     * @test
     */
    public function login_idp_request_valid_signature_unauth_user_unauth_test()
    {
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
            $tokenBase = bin2hex(random_bytes(50));
        } else {
            $randomStringFactory = new \RandomLib\Factory();
            $generator = $randomStringFactory->getMediumStrengthGenerator();
            $tokenBase = $generator->generate(50);
        }

        $encodedT = base64_encode($tokenBase);
        $token = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedT
        );

        $source = factory(Source::class)->create();
        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        $parameters = [
            'source' => $source->url,
            's' => $signature,
            't' => $token
        ];

        $response = $this->get(route('idp.login', $parameters));

        $response->assertStatus(302)
            ->assertSee('login');
    }
}
