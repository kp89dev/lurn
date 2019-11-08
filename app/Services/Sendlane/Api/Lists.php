<?php
namespace App\Services\Sendlane\Api;

class Lists extends AbstractApi
{
    /**
     * @param $page
     * @param $limit
     */
    public function get($page, $limit, $list_id = null)
    {
        return $this->client->request('/api/v1/lists', compact('page', 'limit', 'list_id'));
    }
}
