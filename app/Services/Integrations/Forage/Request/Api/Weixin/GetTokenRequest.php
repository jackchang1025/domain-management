<?php

namespace App\Services\Integrations\Forage\Request\Api\Weixin;


use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Contracts\Body\HasBody;

class GetTokenRequest extends Request  implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;


    public function __construct( protected string $uid,protected string $callback =''){}

    public function resolveEndpoint(): string
    {
        return "/api/weixin/get_token?callback={$this->callback}";
    }

    protected function defaultBody(): array
    {
        return [
            'uid' => $this->uid,
        ];
    }
}