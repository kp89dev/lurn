<?php
namespace App\Http\Controllers\Tracker;

use App\Models\Tracker\Campaign;
use App\Models\Tracker\Identity;
use App\Services\Tracker\Contracts\LocationReader;
use App\Services\Tracker\Contracts\RefererParserInterface;
use DeviceDetector\DeviceDetector;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tracker\Visit;


class IndexController extends Controller
{
    private $request;
    private $campaignValues = [
        'ce_campaign_name',
        'ce_campaign_source',
        'ce_campaign_medium',
        'ce_campaign_content',
        'ce_term'
    ];

    public function index(Request $request)
    {
        $this->request = $request;

        $this->handleIdentity();
        $campaign = $this->handleCampaign();

        return $this->handleVisit($campaign);
    }

    private function handleCampaign()
    {
        foreach ($this->campaignValues as $key => $val) {
            if ($this->request->$val) {
                $hasCampaign = true;
                break;
            }
        }

        if (! isset($hasCampaign)) {
            return null;
        }

        return (new Campaign())->getCampaign(
            $this->request->ce_campaign_name,
            $this->request->ce_campaign_source,
            $this->request->ce_campaign_medium,
            $this->request->ce_campaign_content,
            $this->request->ce_term
        );
    }

    private function handleVisit($campaign)
    {
        $dd = app()->make(DeviceDetector::class);
        $dd->parse();

        $visit = new Visit();

        if ($dd->isBot()) {
            return response('', 204);
        }

        $locationReader = app()->make(LocationReader::class);
        $refererReader  = app()->make(RefererParserInterface::class);
        $client = $dd->getClient();
        $os = $dd->getOs();

        $visit->campaign_id    = $campaign ? $campaign->id : null;
        $visit->city           = $locationReader->city->name;
        $visit->country_iso    = $locationReader->country->isoCode;
        $visit->country_name   = $locationReader->country->name;
        $visit->continent_iso  = $locationReader->continent->code;
        $visit->continent_name = $locationReader->continent->name;
        $visit->time_zone      = $locationReader->location->timeZone;
        $visit->region_iso     = $locationReader->mostSpecificSubdivision->isoCode;
        $visit->region_name    = $locationReader->mostSpecificSubdivision->name;

        $visit->browser_lang    = strtolower($this->request->language);
        $visit->browser_engine  = isset_or($client, 'engine', '');
        $visit->browser_name    = isset_or($client, 'name', '');
        $visit->browser_version = isset_or($client, 'version', '');

        $visit->device_resolution = $this->request->screen;
        $visit->device_brand      = $dd->getBrandName();
        $visit->device_model      = $dd->getModel();
        $visit->device_type       = $dd->getDevice();

        $visit->os          = isset_or($os, 'short_name', '');
        $visit->os_version  = isset_or($os, 'version', '');
        $visit->os_platform = isset_or($os, 'platform', '');

        $visit->referer_url     = $this->request->referer;
        $visit->referer_type    = $refererReader->getMedium();
        $visit->referer_name    = $refererReader->getSource();
        $visit->referer_keyword = $refererReader->getSearchTerm();

        $visit->page_uri    = $this->request->ce_uri;
        $visit->page_domain = $this->request->ce_domain;
        $visit->page_title  = $this->request->ce_title;
        $visit->page_url    = $this->request->ce_url;
        $visit->event_name  = $this->request->event;
        $visit->visitor_id  = ($this->request->visitor ?: $this->request->cookie('__lurn_nation', 'No Id'));

        $i = 1;

        // Get all the parameters that match the ce_* pattern.
        $params = array_where($this->request->all(), function ($item, $key) {
            return str_is('ce_*', $key);
        });

        // Remove the known parameters.
        $params = array_where($params, function ($item, $key) {
            $knownParameters = array_merge($this->campaignValues, ['ce_url', 'ce_title', 'ce_uri', 'ce_domain']);

        	return ! in_array($key, $knownParameters);
        });

        foreach ($params as $requestKey => $requestParam) {
            $visit->{"custom_var_k$i"} = str_after($requestKey, 'ce_');
            $visit->{"custom_var_v$i"} = $requestParam;

            if (++$i > 5) break;
        }

        $visit->save();

        return response('', 204);
    }

    private function handleIdentity()
    {
        if (! $this->request->cv_id) {
            return;
        }

        if (! $this->request->cookie('__lurn_nation')) {
            return;
        }

        (new Identity())->firstOrCreate([
            'user_id'    => $this->request->cv_id,
            'email'      => $this->request->cv_email,
            'visitor_id' => $this->request->cookie('__lurn_nation')
        ]);
    }
}
