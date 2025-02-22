<?php

namespace App\Services\Integrations\Aifabu\Request\Chain;

use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Contracts\Body\HasBody;  
use Saloon\Enums\Method;

class DeleteChainRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        //短链id列表
        public array $chains,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chain/delChain';
    }

    public function defaultBody(): array
    {
        return [
            'chains' => $this->chains,
        ];
    }
    
}