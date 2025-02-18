<?php

namespace App\Services\Integrations\Forage\Resource;

use App\Services\Integrations\Forage\ForageConnector;
use App\Services\Integrations\Forage\Request\Login\EntryRequest;
use Saloon\Http\Response;
use App\Services\Integrations\Forage\Request\Api\VisitorAnalysis\GetCvidRequest;
use App\Services\Integrations\Forage\Request\Api\Weixin\GetTokenRequest;

class LoginResource
{
    public function __construct(readonly protected ForageConnector $connector)
    {
        //
    }

    public function getConnector(): ForageConnector
    {
        return $this->connector;
    }

    public function entry(string $account, string $password): Response
    {
        return $this->connector->send(new EntryRequest($account, $password));
    }

    public function getCvid(): Response
    {
        return $this->connector->send(new GetCvidRequest());
    }

    public function getToken(string $uid, string $callback = ''): Response
    {
        return $this->connector->send(new GetTokenRequest($uid, $callback));
    }
}
