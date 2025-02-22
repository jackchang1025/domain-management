<?php

namespace App\Services\Integrations\Aifabu\Request\Group;

use Saloon\Http\Request;
use Saloon\Contracts\Body\HasBody;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Enums\Method;
use App\Services\Integrations\Aifabu\Enums\ChainType;

class CreateGroupRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $name,
        protected ChainType $chainType = ChainType::SHORT_LINK,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chainGroup/createChainGroup';
    }

    public function defaultBody(): array
    {
        return [
            'name' => $this->name,
            'chain_type' => $this->chainType->value,
        ];
    }
}
