<?php

namespace App\Services\Integrations\Aifabu\Request\Chain;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;
use Saloon\Contracts\Body\HasBody;


class CreateChainRequest extends Request  implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        //跳转目标链接 必须以 http:// 或 https:// 开头的链接
        protected string $target_url,
        //短链分组id 获取短链分组接口中获取，不填则默认使用账号的首个分组
        protected int $groupId,
        //专属域名 
        protected ?string $domain = null,
        //短链标题 不填则默认为'未命名'
        protected ?string $chain_title = null,
        //短链到期时间 不传即为永久有效
        protected ?string $valid_time = null,
    ) {
        //
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chain/createChain';
    }

    public function defaultBody(): array
    {
        return [
            'target_url' => $this->target_url,
            'group_id' => $this->groupId,
            'domain' => $this->domain,
            'chain_title' => $this->chain_title,
            'valid_time' => $this->valid_time,
        ];
    }
}
