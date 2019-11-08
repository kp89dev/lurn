<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\ValidateCard;
use App\Models\InfusionsoftContact;
use Infusionsoft\Api\InvoiceService;
use Infusionsoft\Infusionsoft;
use Mockery;

class ValidateCardTest extends \TestCase
{
    /**
     * @test
     */
    public function infusionsoft_throws_exception()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $card = new \stdClass();
        
        $card->type = 'Visa';
        $card->number = 12341234;
        $card->cvv = 101;
        $card->expMonth = 10;
        $card->expYear = 22;
        $card->nameOnCard = 'Marius';

        $isMock = Mockery::mock(Infusionsoft::class);
        $invoiceService = Mockery::mock(InvoiceService::class);

        $isMock->shouldReceive('invoices')->andReturn($invoiceService);
        $invoiceService->shouldReceive('validateCreditCard')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new ValidateCard($contact, $card);
        $result = $job->handle();

        $this->assertInstanceOf(ValidateCard::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class, $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function valid_card_infusionsoft_returns_error()
    {
        $contact = factory(InfusionsoftContact::class)->make();
        $card = new \stdClass();

        $card->type = 'Visa';
        $card->number = 12341234;
        $card->cvv = 101;
        $card->expMonth = 10;
        $card->expYear = 22;
        $card->nameOnCard = 'Marius';

        $isMock = Mockery::mock(Infusionsoft::class);
        $invoiceService = Mockery::mock(InvoiceService::class);

        $isMock->shouldReceive('invoices')->andReturn($invoiceService);
        $invoiceService->shouldReceive('validateCreditCard')->andReturn([
            'Valid' => "false",
            "Message" => "Credit Card Expired"
        ]);

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new ValidateCard($contact, $card);
        $result = $job->handle();

        $this->assertInstanceOf(ValidateCard::class, $result);
        $this->assertEquals("Credit Card Expired", $job->getError());
    }
}
