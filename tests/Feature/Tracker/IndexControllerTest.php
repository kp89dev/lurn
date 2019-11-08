<?php
namespace Feature\Tracker;

use App\Services\Tracker\Contracts\LocationReader;
use App\Services\Tracker\Contracts\RefererParserInterface;
use DeviceDetector\DeviceDetector;

class IndexControllerTest extends \TestCase
{
    /**
     * @test
     */
    public function tracker_handles_identity_correctly()
    {
        $data = [
            'cv_id'    => 123,
            'cv_email' => 'test@test.com'
        ];

        $dd = $this->createMock(DeviceDetector::class);
        $dd->expects(self::once())
            ->method('isBot')
            ->willReturn(true);

        $this->app->bind(DeviceDetector::class, function($app) use ($dd) {
            return $dd;
        });

        $response = $this->call('GET', route('internal-tracker'), $data, ['__lurn_nation' => 'ABCDEFG']);
        $response->assertStatus(204);

        $this->assertDatabaseHas('tr_identities', [
            'user_id' => $data['cv_id'],
            'email'   => $data['cv_email'],
            'visitor_id' => 'ABCDEFG'
        ]);
    }

    /**
     * @test
     */
    public function tracker_doesnt_add_identity_when_user_id_is_missing()
    {
        $data = [
            'cv_email' => 'test@test.com'
        ];

        $dd = $this->createMock(DeviceDetector::class);
        $dd->expects(self::once())
            ->method('isBot')
            ->willReturn(true);

        $this->app->bind(DeviceDetector::class, function($app) use ($dd) {
            return $dd;
        });

        $response = $this->call('GET', route('internal-tracker'), $data, ['__lurn_nation' => 'ABCDEFG']);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('tr_identities', [
            'email'   => $data['cv_email'],
            'visitor_id' => 'ABCDEFG'
        ]);
    }

    /**
     * @test
     */
    public function tracker_adds_campaign_params_correctly()
    {
        $data = [
            'ce_campaign_name'    => 'campaign_name',
            'ce_campaign_source'  => 'campaign_source',
            'ce_campaign_medium'  => 'campaign_medium',
            'ce_campaign_content' => 'campaign_content',
            'ce_term'             => 'campaign_term'
        ];
        $dd = $this->createMock(DeviceDetector::class);
        $dd->expects(self::once())
            ->method('isBot')
            ->willReturn(true);

        $this->app->bind(DeviceDetector::class, function($app) use ($dd) {
            return $dd;
        });
        
        $response = $this->call('GET', route('internal-tracker'), $data, ['__lurn_nation' => 'ABCDEFG']);
        $response->assertStatus(204);

        $this->assertDatabaseMissing('tr_campaigns', [
            'name'   => $data['ce_campaign_name'],
            'source'   => $data['ce_campaign_source'],
            'medium'   => $data['ce_campaign_medium'],
            'content'   => $data['ce_campaign_content'],
            'term'   => $data['ce_term'],
        ]);
    }

    /**
     * @test
     */
    public function tracker_tracks_visit_successfully()
    {
        $dd = $this->createMock(DeviceDetector::class);
        $dd->expects(self::once())
            ->method('isBot')
            ->willReturn(false);

        $dd->expects(self::any())
            ->method('getClient')
            ->willReturn([
                'engine'     => 'engine',
                'version'    => '1.2',
                'name'       => 'name'
            ]);
        $dd->expects(self::any())
            ->method('getOs')
            ->willReturn([
                'version'    => '1.2',
                'short_name' => 'short_name',
                'platform'   => 'platform'
            ]);
        $dd->expects(self::once())->method('getBrandName')->willReturn('brand_name');
        $dd->expects(self::once())->method('getModel')->willReturn('model');
        $dd->expects(self::once())->method('getDevice')->willReturn(3);

        $this->app->bind(DeviceDetector::class, function($app) use ($dd) {
            return $dd;
        });

        $location = new LocationMock();
        $this->app->bind(LocationReader::class, function($app) use ($location) {
           return $location;
        });

        $referer = $this->createMock(RefererParserInterface::class);
        $referer->expects(self::once())->method('getMedium')->willReturn('google');
        $referer->expects(self::once())->method('getSource')->willReturn('source');
        $referer->expects(self::once())->method('getSearchTerm')->willReturn('searchTerm');
        $this->app->bind(RefererParserInterface::class, function ($app) use ($referer) {
            return $referer;
        });

        $data = [
            'language'  => 'en-us',
            'event'     => 'pv',
            'referer'   => 'http://referer.com',
            'ce_uri'    => 'http://self.com/dashboard',
            'ce_domain' => 'self.com',
            'ce_title'  => 'some_title',
            'ce_url'    => '/dashboard',
            'screen'    => '1600x800',
            'ce_param1' => 'value1',
            'ce_param2' => 'value2',
            'ce_param3' => 'value3',
            'ce_param4' => 'value4',
            'ce_param5' => 'value5'
        ];

        $response = $this->call('GET', route('internal-tracker'), $data, ['__lurn_nation' => 'ABCDEFG']);
        $response->assertStatus(204);

        $this->assertDatabaseHas('tr_visits', [
            'city'              => 'city_name',
            'country_iso'       => 'country_isoCode',
            'country_name'      => 'country_name',
            'continent_iso'     => 'continent_code',
            'continent_name'    => 'continent_name',
            'time_zone'         => 'location_timeZone',
            'region_iso'        => 'mostSpecificSubdivis', //sliced by db length
            'region_name'       => 'mostSpecificSubdivision_n', //sliced by db length
            'browser_lang'      => 'en-us',
            'browser_engine'    => 'engine',
            'browser_name'      => 'name',
            'browser_version'   => '1.2',
            'device_resolution' => '1600x800',
            'device_brand'      => 'brand_name',
            'device_model'      => 'model',
            'device_type'       => 3,
            'os'                => 'sho', //sliced by db length
            'os_version'        => '1.2',
            'os_platform'       => 'platform',
            'referer_url'       => 'http://referer.com',
            'referer_type'      => 'google',
            'referer_name'      => 'source',
            'referer_keyword'   => 'searchTerm',
            'page_uri'          => 'http://self.com/dashboard',
            'page_domain'       => 'self.com',
            'page_title'        => 'some_title',
            'page_url'          => '/dashboard',
            'event_name'        => 'pv',
            'visitor_id'        => 'ABCDEFG',
            'custom_var_v1'     => 'value1',
            'custom_var_v2'     => 'value2',
            'custom_var_v3'     => 'value3',
            'custom_var_v4'     => 'value4',
            'custom_var_v5'     => 'value5'
        ]);
    }
}

class LocationMock implements LocationReader
{
    public function __get($param) {
        return new class($param) {
            private $parent;

            public function __construct($param)
            {
                $this->parent = $param;
            }

            public function __get($p)
            {
                return $this->parent . '_' . func_get_args()[0];
            }
        };
    }
}
