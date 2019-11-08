<?php
/**
 * Date: 3/19/18
 * Time: 11:26 AM
 */

namespace Tests\Unit\Queries;

use App\Commands\Infusionsoft\Refunds;
use App\Console\Commands\Infusionsoft\RefundActivity;
use App\Models\Course;
use App\Models\CourseInfusionsoft;
use App\Models\InfusionsoftContact;
use App\Models\InfusionsoftToken;
use App\Models\Queries\ProcessRefunds;
use App\Models\User;
use App\Models\UserCourse;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use TestCase;
use Mockery as m;

class ProcessRefundsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function testProcessRefundsResults()
    {
        $refunds = $this->getRefunds();
        $this->assertNotNull($refunds);

        foreach ($refunds->getRefunds() as $key => $refund) {
            /** @var User $user */
            $user = factory(User::class)->create();

            factory(InfusionsoftContact::class)->create([
                'is_contact_id' => $refund->get('ContactId'),
                'user_id' => $user->id
            ]);

            $course = factory(Course::class)->create();

            $invoice = $refund->get('Invoice');
            factory(CourseInfusionsoft::class)->create([
                'is_product_id' => $invoice->get('ProductSold'),
                'course_id' => $course->id
            ]);

            if (!$key) {
                factory(UserCourse::class)->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'created_at' => Carbon::now(),
                    'status' => 0,
                ]);
            }
        }

        /** @var ProcessRefunds $processor */
        $processor = app(ProcessRefunds::class);
        $processor->setRefundsHandler($refunds);
        $processor->process();
    }

    /**
     * @test
     */
    public function testProcessRefundsResultsFails()
    {
        $refunds = $this->getRefunds();
        $this->assertNotNull($refunds);

        foreach ($refunds->getRefunds() as $key => $refund) {
            /** @var User $user */
            $user = factory(User::class)->create();

            factory(InfusionsoftContact::class)->create([
                'is_contact_id' => $refund->get('ContactId'),
                'user_id' => $user->id
            ]);

            $course = factory(Course::class)->create();

            $invoice = $refund->get('Invoice');
            factory(CourseInfusionsoft::class)->create([
                'is_product_id' => $invoice->get('ProductSold'),
                'course_id' => $course->id
            ]);

            if (!$key) {
                factory(UserCourse::class)->create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'created_at' => Carbon::now(),
                    'status' => 0,
                ]);
            }
        }

        $db = m::mock('Illuminate\Database\DatabaseManager');
        $db->shouldReceive('beginTransaction')->once()->andThrow('\Exception');
        $db->shouldReceive('rollBack')->once()->andReturnSelf();
        $this->app->instance('Illuminate\Database\DatabaseManager', $db);

        /** @var ProcessRefunds $processor */
        $processor = app(ProcessRefunds::class);
        $processor->setRefundsHandler($refunds);
        $processor->process();
    }

    /**
     * @return Refunds|m\Mock
     */
    private function getRefunds()
    {
        /** @var Collection $tokens */
        $tokens = factory(InfusionsoftToken::class, 2)->create();
        $date = Carbon::now()->subMinute(1);

        $page = 0;
        $responseQueries = [];
        $invoiceQueries = [];

        $responseRefunds = $this->getResponseRefunds();
        $responseInvoices = $this->getInvoices($responseRefunds);

        foreach ($tokens as $infusionsoftToken) {
            /** @var Collection $users */
            $users = factory(User::class, 2)->create();
            $infusionsoftContacts = [];

            $users->each(function (User $user) use ($infusionsoftToken, &$infusionsoftContacts) {
                /** @var InfusionsoftContact $infusionsoftContact */
                $infusionsoftContact = factory(InfusionsoftContact::class)->create([
                    'user_id' => $user->id,
                    'is_contact_id' => $this->faker->randomNumber(6),
                    'is_account' => $infusionsoftToken->account,
                ]);
                array_push($infusionsoftContacts, (int)$infusionsoftContact->is_contact_id);
            });

            asort($infusionsoftContacts);
            $infusionsoftContacts = array_values($infusionsoftContacts);

            array_push($responseQueries, [
                'Payment',
                500,
                $page,
                [
                    'LastUpdated' => "~>~ {$date->toDateTimeString()}",
                    'PayType' => 'Refund',
                    'ContactId' => $infusionsoftContacts,
                ],
                [
                    'Id',
                    'ContactId',
                    'InvoiceId',
                    'PayAmt',
                    'PayType',
                    'RefundId',
                    'LastUpdated',
                ],
                'LastUpdated',
                true
            ]);

            array_push($invoiceQueries, [
                'Invoice',
                500,
                0,
                [
                    'Id' => [(int)collect($responseRefunds[$page])->get('InvoiceId')],
                ],
                [
                    'Id',
                    'InvoiceTotal',
                    'InvoiceType',
                    'PayPlanStatus',
                    'PayStatus',
                    'RefundStatus',
                    'TotalPaid',
                    'ProductSold',
                ],
                'Id',
                true
            ]);

            $page++;
        }

        $infusionsoft = m::mock('Infusionsoft\Infusionsoft');
        $infusionsoft->shouldReceive('data')->andReturnSelf();
        $infusionsoft->shouldReceive('query')->withArgs($responseQueries[0])->andReturn([$responseRefunds[0]]);
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[0])->andReturn([$responseInvoices[0]]);
        $infusionsoft->shouldReceive('query')->withArgs($responseQueries[1])->andReturn([$responseRefunds[1]]);
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[1])->andReturn([$responseInvoices[1]]);

        $refunds = m::mock('App\Commands\Infusionsoft\Refunds[getIsAccount]')->makePartial();
        $refunds->shouldReceive('getIsAccount')->andReturn($infusionsoft);
        app()->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        /** @var Refunds $refunds */
        $refunds = $this->app->make(Refunds::class);
        $refunds->setDate($date)
            ->setIdentifier(sha1(time()))
            ->setCommand(app(RefundActivity::class))
            ->process();

        return $refunds;
    }

    /**
     * @return array
     */
    private function getResponseRefunds()
    {
        return [
            [
                'PayAmt' => -697.0,
                'Id' => $this->faker->numberBetween(100000, 999999),
                'ContactId' => $this->faker->numberBetween(100000, 999999),
                'PayType' => 'Refund',
                'RefundId' => 0,
                'LastUpdated' => new DateTime('2018-02-12 09:42:29'),
                'InvoiceId' => "{$this->faker->numberBetween(100000, 999999)}",
            ],
            [
                'PayAmt' => -698.0,
                'Id' => $this->faker->numberBetween(100000, 999999),
                'ContactId' => $this->faker->numberBetween(100000, 999999),
                'PayType' => 'Refund',
                'RefundId' => 0,
                'LastUpdated' => new DateTime('2018-03-15 13:51:08'),
                'InvoiceId' => "{$this->faker->numberBetween(100000, 999999)}",
            ],
        ];
    }

    /**
     * @param $responseRefunds
     * @return mixed
     */
    private function getInvoices($responseRefunds)
    {
        $responseInvoices = [];

        foreach (convert_to_collection($responseRefunds) as $responseRefund) {
            array_push($responseInvoices, [
                'Id' => (int)$responseRefund->get('InvoiceId'),
                'InvoiceTotal' => 697.0,
                'InvoiceType' => 'Online',
                'PayPlanStatus' => 1,
                'PayStatus' => 1,
                'RefundStatus' => 2,
                'TotalPaid' => 697.0,
                'ProductSold' => "47",
            ]);
        }

        return $responseInvoices;
    }
}