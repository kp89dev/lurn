<?php
/**
 * Date: 3/19/18
 * Time: 11:26 AM
 */

namespace Tests\Unit\Commands;

use App\Commands\Infusionsoft\Refunds;
use App\Console\Commands\Infusionsoft\RefundActivity;
use App\Models\InfusionsoftContact;
use App\Models\InfusionsoftToken;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use TestCase;
use Mockery as m;

class RefundsTest extends TestCase
{
    use DatabaseTransactions;

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

    /**
     * @test
     */
    public function testProcessRefunds()
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

        $this->assertNotNull($refunds->getRefunds());
        $this->assertNotNull($refunds->getCommand());
    }

    /**
     * @test
     */
    public function testProcessRefundsFailWithFirstException()
    {
        factory(InfusionsoftToken::class, 2)->create();

        $date = Carbon::now()->subMinute(1);

        $responseRefunds = [
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

        $infusionsoft = m::mock('Infusionsoft\Infusionsoft[data, query]')->makePartial();
        $infusionsoft->shouldReceive('data')->andReturnSelf();

        $infusionsoft->shouldReceive('query')->withArgs([
            'Payment',
            500,
            0,
            [
                'LastUpdated' => "~>~ {$date->toDateTimeString()}",
                'PayType' => 'Refund',
                'ContactId' => [],
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
        ])->andReturn($responseRefunds);

        $infusionsoft->shouldReceive('query')->withArgs([
            'Invoice',
            500,
            0,
            [
                'Id' => collect($responseRefunds)->pluck('InvoiceId')->map(function ($data) {
                    return (int) $data;
                })->toArray(),
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
        ])->andReturn($responseInvoices);

        $refunds = m::mock('App\Commands\Infusionsoft\Refunds[getIsAccount,outputMessage]')->makePartial();
        $refunds->shouldReceive('getIsAccount')->andReturn($infusionsoft);
        $refunds->shouldReceive('outputMessage')->with('Querying Infusionsoft for refunds...')->andThrow('\Exception');
        $refunds->shouldReceive('outputMessage')->with('There was a problem fetching the Infusionsoft refunds.');
        app()->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        $refunds = m::mock('Eloquent', 'App\Models\InfusionsoftToken[all]')->makePartial();
        $refunds->shouldReceive('all')->andThrow('\Exception');
        app()->instance('App\Models\InfusionsoftToken', $refunds);

        /** @var Refunds $refunds */
        $refunds = app(Refunds::class);
        $refunds->setDate($date)
            ->setIdentifier(sha1(time()))
            ->setCommand(app(RefundActivity::class))
            ->process();
    }

    /**
     * @test
     */
    public function testProcessRefundsFailWithPaymentException()
    {
        $token = factory(InfusionsoftToken::class)->create();

        $users = factory(User::class, 2)->create();
        $infusionsoftContacts = [];

        $users->each(function (User $user) use ($token, &$infusionsoftContacts) {
            /** @var InfusionsoftContact $infusionsoftContact */
            $infusionsoftContact = factory(InfusionsoftContact::class)->create([
                'user_id' => $user->id,
                'is_contact_id' => $this->faker->randomNumber(6),
                'is_account' => $token->account,
            ]);
            array_push($infusionsoftContacts, (int)$infusionsoftContact->is_contact_id);
        });

        $date = Carbon::now()->subMinute(1);

        $responseRefunds = [
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

        $infusionsoft = m::mock('Infusionsoft\Infusionsoft[data, query]')->makePartial();
        $infusionsoft->shouldReceive('data')->andReturnSelf();

        $infusionsoft->shouldReceive('query')->withArgs([
            'Payment',
            500,
            0,
            [
                'LastUpdated' => "~>~ {$date->toDateTimeString()}",
                'PayType' => 'Refund',
                'ContactId' => [],
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
        ])->andReturn($responseRefunds);

        $infusionsoft->shouldReceive('query')->withArgs([
            'Invoice',
            500,
            0,
            [
                'Id' => collect($responseRefunds)->pluck('InvoiceId')->map(function ($data) {
                    return (int) $data;
                })->toArray(),
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
        ])->andThrow('\Exception');

        $refunds = m::mock('App\Commands\Infusionsoft\Refunds[getIsAccount,outputMessage]')->makePartial();
        $refunds->shouldReceive('getIsAccount')->andReturn($infusionsoft);
        $refunds->shouldReceive('outputMessage')->with('There was a problem fetching the Infusionsoft refunds.');
        app()->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        $refunds = m::mock('Eloquent', 'App\Models\InfusionsoftToken[all]')->makePartial();
        $refunds->shouldReceive('all')->andThrow('\Exception');
        app()->instance('App\Models\InfusionsoftToken', $refunds);

        /** @var Refunds $refunds */
        $refunds = app(Refunds::class);
        $refunds->setDate($date)
            ->setIdentifier(sha1(time()))
            ->setCommand(app(RefundActivity::class))
            ->process();
    }

    /**
     * @test
     */
    public function testProcessRefundsFailWithInvoiceException()
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
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[0])->andThrow('\Exception');
        $infusionsoft->shouldReceive('query')->withArgs($responseQueries[1])->andReturn([$responseRefunds[1]]);
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[1])->andThrow('\Exception');

        $refunds = m::mock('App\Commands\Infusionsoft\Refunds[getIsAccount]')->makePartial();
        $refunds->shouldReceive('getIsAccount')->andReturn($infusionsoft);
        app()->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        /** @var Refunds $refunds */
        $refunds = $this->app->make(Refunds::class);
        $refunds->setDate($date)
            ->setIdentifier(sha1(time()))
            ->setCommand(app(RefundActivity::class))
            ->process();
    }

    /**
     * @test
     */
    public function testProcessRefundsFailWithTokenExpiredException()
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
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[0])
            ->andThrow('\Infusionsoft\TokenExpiredException');
        $infusionsoft->shouldReceive('query')->withArgs($responseQueries[1])->andReturn([$responseRefunds[1]]);
        $infusionsoft->shouldReceive('query')->withArgs($invoiceQueries[1])
            ->andThrow('\Infusionsoft\TokenExpiredException');

        $refunds = m::mock('App\Commands\Infusionsoft\Refunds[getIsAccount]')->makePartial();
        $refunds->shouldReceive('getIsAccount')->andReturn($infusionsoft);
        app()->instance('App\Commands\Infusionsoft\Refunds', $refunds);

        /** @var Refunds $refunds */
        $refunds = $this->app->make(Refunds::class);
        $refunds->setDate($date)
            ->setIdentifier(sha1(time()))
            ->setCommand(app(RefundActivity::class))
            ->process();
    }
}