<?php

namespace App\Services\Integrations\Aifabu\Resource;

use App\Services\Integrations\Aifabu\AifabuConnector;
use Saloon\Http\Response;
use App\Services\Integrations\Aifabu\Request\Chain\CreateChainRequest;
use App\Services\Integrations\Aifabu\Request\Chain\DeleteChainRequest;
use App\Services\Integrations\Aifabu\Request\Chain\EditChainRequest;
use App\Services\Integrations\Aifabu\Request\Chain\ListChainRequest;

class ChainResource
{
    public function __construct(protected AifabuConnector $connector){}

    public function create(string $targetUrl, ?int $groupId = null, string $domain = null, string $chainTitle = null, string $validTime = null): Response
    {
        return $this->connector->send(new CreateChainRequest($targetUrl, $groupId, $domain, $chainTitle, $validTime));
    }

    public function delete(array $chains): Response
    {
        return $this->connector->send(new DeleteChainRequest($chains));
    }

    public function update(string $chain, string $targetUrl, string $chainTitle): Response
    {
        return $this->connector->send(new EditChainRequest($targetUrl, $chainTitle, $chain));
    }

    public function list(int $groupId, int $start = 0, int $count = 100, int $beginDate = null, int $endDate = null): Response
    {
        return $this->connector->send(new ListChainRequest($groupId, $start, $count, $beginDate, $endDate));
    }
}
