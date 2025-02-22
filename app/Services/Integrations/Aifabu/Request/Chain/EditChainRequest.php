<?php

namespace App\Services\Integrations\Aifabu\Request\Chain;

use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

class EditChainRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;


    public function __construct(
        //必须以 http:// 或 https:// 开头的链接
        public string $target_url,
        //短链标题
        public string $chain_title,
        //短链
        public string $chain,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chain/editChain';
    }

    public function defaultBody(): array
    {
        return [    
            'chain' => $this->chain,
            'chain_title' => $this->chain_title,
            'target_url' => $this->target_url,
        ];
    }
}
