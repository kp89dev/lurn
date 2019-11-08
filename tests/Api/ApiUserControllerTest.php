<?php
namespace Tests\Api;

use App\Models\Source;
use App\Models\SourceEmail;
use App\Models\SourceToken;
use Carbon\Carbon;

class ApiUserControllerTest extends \LoggedInTestCase
{
    /**
     * @test
     */
    public function api_no_vars_fail_test()
    {
        $response = $this->get(url('api/v1/user'));
        $response->assertSee('Referer was not found');
    }

    /**
     * @test
     */
    public function api_invalid_referer_fail_test()
    {
        $response = $this->get(url('api/v1/user'), ['HTTP_Referer' => 'notagoodreferer.com']);
        $response->assertSee('Requester is not allowed to access the data #1');
    }

    /**
     * @test
     */
    public function api_invalid_ip_fail_test()
    {
        $source = factory(Source::class)->create();
        $response = $this->get(url('api/v1/user'), ['HTTP_Referer' => $source->url]);
        $response->assertSee('Requester is not allowed to access the data #2');
    }

    /**
     * @test
     */
    public function api_valid_ip_no_signature_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);
        $response = $this->get(url('api/v1/user'), ['HTTP_Referer' => $source->url]);
        $response->assertSee('Signature was not found');
    }

    /**
     * @test
     */
    public function api_valid_ip_no_token_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);
        $response = $this->get(url('api/v1/user'), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => 'invalid']);
        $response->assertSee('Token was not found');
    }

    /**
     * @test
     */
    public function api_valid_ip_invalid_vars_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);
        $response = $this->get(url('api/v1/user/?token=invalid'), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => 'invalid']);
        $response->assertSee('Invalid signature');
    }

    /**
     * @test
     */
    public function api_valid_ip_valid_vars_no_sourcetoken_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);

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

        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        $response = $this->get(url('api/v1/user/?token=' . $token), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => $signature]);
        $response->assertSee('Invalid token provided.');
    }

    /**
     * @test
     */
    public function api_valid_ip_valid_vars_used_token_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);

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

        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        factory(SourceToken::class)->create(['token' => $tokenBase, 'used' => 1]);

        $response = $this->get(url('api/v1/user/?token=' . $token), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => $signature]);
        $response->assertSee('Token already used');
    }

    /**
     * @test
     */
    public function api_valid_ip_valid_vars_expired_token_fail_test()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);

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

        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        factory(SourceToken::class)->create([
            'token'      => $tokenBase,
            'created_at' => Carbon::now()->subMinutes(5),
            'used'       => 0,
        ]);

        $response = $this->get(url('api/v1/user/?token=' . $token), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => $signature]);
        $response->assertSee('Token expired');
    }

    /**
     * @test
     */
    public function api_valid_ip_valid_vars_gives_user_email()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);
        $tokenBase = bin2hex(random_bytes(50));

        $encodedT = base64_encode($tokenBase);
        $token = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedT
        );

        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        factory(SourceToken::class)->create(['token' => $tokenBase, 'user_id' => $this->user->id]);

        $response = $this->get(url('api/v1/user/?token=' . $token), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => $signature]);
        $response->assertStatus(200)->assertSee($this->user->email);
    }

    /**
     * @test
     */
    public function api_valid_ip_valid_vars_gives_source_email()
    {
        $localIP = $this->getTestingIp();
        $source = factory(Source::class)->create(['ip' => $localIP]);
        $tokenBase = bin2hex(random_bytes(50));
        $sourceEmail = factory(SourceEmail::class)->create([
            'user_id' => $this->user->id,
            'source_id' => $source->id
        ]);

        $encodedT = base64_encode($tokenBase);
        $token = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedT
        );

        $data = $source->url . $tokenBase . $source->token;
        $signatureBase = hash_hmac('sha256', $data, $source->token);

        $encodedSig = base64_encode($signatureBase);
        $signature = str_replace(
            ['+', '=', '/'], ['-', '_', '~'], $encodedSig
        );

        $source->sourceToken()->create(['token' => $tokenBase, 'user_id' => $this->user->id]);

        $response = $this->get(url('api/v1/user/?token=' . $token), ['HTTP_Referer' => $source->url, 'HTTP_X-Lurn-Signature' => $signature]);
        $response->assertStatus(200)
                 ->assertDontSee($this->user->email)
                 ->assertSee($sourceEmail->email);

    }


    /**
     * @return string
     */
    private function getTestingIp()
    {
        return env('TESTING_IP') ?? getHostByName(getHostName());
    }
}
