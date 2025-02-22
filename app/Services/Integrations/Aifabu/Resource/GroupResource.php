<?php

namespace App\Services\Integrations\Aifabu\Resource;

use App\Services\Integrations\Aifabu\AifabuConnector;
use App\Services\Integrations\Aifabu\Request\Group\CreateGroupRequest;
use App\Services\Integrations\Aifabu\Enums\ChainType;
use Saloon\Http\Response;
use App\Services\Integrations\Aifabu\Request\Group\ListGroupRequestRequest;

class GroupResource
{
    public function __construct(protected AifabuConnector $connector){}

    public function createGroup(string $name, ChainType $chainType = ChainType::SHORT_LINK): Response
    {
        return $this->connector->send(new CreateGroupRequest($name, $chainType));
    }

    public function getGroupList(ChainType $chainType = ChainType::SHORT_LINK): Response
    {
        return $this->connector->send(new ListGroupRequestRequest($chainType));
    }
}
