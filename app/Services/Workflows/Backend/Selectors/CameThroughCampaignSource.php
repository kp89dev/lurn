<?php
namespace App\Services\Workflows\Backend\Selectors;

use App\Services\Workflows\Backend\Selectors\Contracts\SelectorAbstract;
use App\Services\Workflows\Backend\Selectors\Contracts\SelectorContract;

class CameThroughCampaignSource  extends SelectorAbstract implements SelectorContract
{
    public function join($query, $values)
    {
        return $query->join('tr_identities as ' . $this->getAlias('tr_identities'), $this->getAlias('tr_identities') . '.user_id', '=', 'users.id')
            ->join('tr_visits as ' . $this->getAlias('tr_visits'), $this->getAlias('tr_visits') . '.visitor_id', '=', $this->getAlias('tr_identities') . '.visitor_id')
            ->join('tr_campaigns as ' . $this->getAlias('tr_campaigns'), $this->getAlias('tr_campaigns') . '.id', '=', $this->getAlias('tr_visits') . '.campaign_id');
    }

    public function where($query, $value)
    {
        return $query->where($this->getAlias('tr_campaigns') . '.source', '=', $value[0]['value']);
    }
}
