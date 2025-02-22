<?php

namespace App\Services\Integrations\Forage;

use App\Services\Integrations\Forage\Resource\LoginResource;
use App\Services\Integrations\Forage\Resource\DomainResource;
use Saloon\Http\Connector;
use App\Services\Integrations\Trait\HasLogger;
use App\Services\Integrations\Trait\HasCookie;
class ForageConnector extends Connector
{
    use HasLogger;
    use HasCookie;

    public function resolveBaseUrl(): string
    {
        return 'https://user.cli.im';
    }

    public function getLoginResource(): LoginResource
    {
        return new LoginResource($this);
    }

    public function getDomainResource(): DomainResource
    {
        return new DomainResource($this);
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"Windows"',
            'Accept-Encoding' => 'gzip, deflate, br, zstd',
            'Accept-Language' => 'en,zh-CN;q=0.9,zh;q=0.8',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Host' => 'user.cli.im',
            'Origin' => 'https://user.cli.im',
            'Referer' => 'https://user.cli.im/login?iframe=true&isactives=31&refer_from=home&withtip=&is_new=1&has_demo=0',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];
    }
}
