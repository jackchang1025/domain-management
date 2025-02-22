<?php

namespace App\Services\Integrations\Aifabu\Request\Group;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use App\Services\Integrations\Aifabu\Enums\ChainType;
use App\Services\Integrations\Aifabu\Data\Response\Group\Groups;
use Saloon\Http\Response;

class ListGroupRequestRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected ChainType $chainType = ChainType::SHORT_LINK,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chainGroup/getChainGroup';
    }

    public function createDtoFromResponse(Response $response): Groups
    {
        return Groups::from($response->json());
    }

    public function defaultQuery(): array
    {
        return [
            'chain_type' => $this->chainType->value,
        ];
    }
}