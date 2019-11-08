<?php
namespace App\Services\Sendlane\Api;


class Subscribers extends AbstractApi
{
    public function add(string $email, int $list_id)
    {
        return $this->client->request('/api/v1/list-subscriber-add', compact('email', 'list_id'));
    }
}
