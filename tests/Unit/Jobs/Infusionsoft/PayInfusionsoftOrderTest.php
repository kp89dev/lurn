<?php
namespace Tests\Unit\Jobs\Infusionsoft;

use App\Jobs\Infusionsoft\PayInfusionsoftOrder;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftMerchantId;
use Infusionsoft\Api\InvoiceService;
use Infusionsoft\Infusionsoft;
use Mockery;

class PayInfusionsoftOrderTest extends \TestCase
{
    /**
     * @test
     */
    public function infusionsoft_throws_exception()
    {
        $course = factory(Course::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'is_account' => 'uv222'
        ]);
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uv222'
        ]);

        $card = new \stdClass();

        $isMock = Mockery::mock(Infusionsoft::class);
        $invoiceService = Mockery::mock(InvoiceService::class);

        $card->Id = 1;
        $invoiceId = 1;
        $isMock->shouldReceive('invoices')->andReturn($invoiceService);
        $invoiceService->shouldReceive('chargeInvoice')->andThrow(\Exception::class, 'An error message');

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new PayInfusionsoftOrder($course, $card, $invoiceId);
        $result = $job->handle();

        $this->assertInstanceOf(PayInfusionsoftOrder::class, $result);
        $this->assertTrue(is_string($job->getError()));
        $this->assertInstanceOf(\Exception::class, $job->getException());
        $this->assertEquals('An error message', $job->getException()->getMessage());
    }

    /**
     * @test
     */
    public function infusionsoft_returns_error()
    {
        $course = factory(Course::class)->create();
        factory(CourseInfusionsoft::class)->create([
            'course_id' => $course->id,
            'is_account' => 'uv222'
        ]);
        factory(InfusionsoftMerchantId::class)->create([
            'account' => 'uv222'
        ]);
        $card = new \stdClass();
        $card->Id = 1;
        $invoiceId = 1;

        $isMock = Mockery::mock(Infusionsoft::class);
        $invoiceService = Mockery::mock(InvoiceService::class);

        $isMock->shouldReceive('invoices')->andReturn($invoiceService);
        $invoiceService->shouldReceive('chargeInvoice')->andReturn([
            'Message' => 'Cancelled: Data format problem',
            'Successful' => false,
            'Code' => 'Error',
            'RefNum' => '',
        ]);

        $this->app->bind(Infusionsoft::class, function ($app) use ($isMock) {
            return $isMock;
        });

        $job = new PayInfusionsoftOrder($course, $card, $invoiceId);
        $result = $job->handle();

        $this->assertInstanceOf(PayInfusionsoftOrder::class, $result);
        $this->assertEquals('Cancelled: Data format problem', $job->getError());
    }
}
