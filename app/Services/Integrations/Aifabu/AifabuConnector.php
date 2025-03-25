<?php

namespace App\Services\Integrations\Aifabu;

use Saloon\Http\Connector;
use Weijiajia\SaloonphpLogsPlugin\HasLogger;
use Saloon\Http\Response;
use App\Services\Integrations\Aifabu\Resource\GroupResource;
use App\Services\Integrations\Aifabu\Resource\ChainResource;
use Saloon\Http\Auth\QueryAuthenticator;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use App\Services\Integrations\Trait\HasCookie;
use Weijiajia\SaloonphpLogsPlugin\Contracts\HasLoggerInterface;

class AifabuConnector extends Connector implements HasLoggerInterface
{
    use HasLogger;
    use AlwaysThrowOnErrors;

    public function __construct(public readonly string $token) {
        //
    }


    protected function defaultAuth(): QueryAuthenticator
    {
        return new QueryAuthenticator('apikey', $this->token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://openapi.aifabu.com';
    }

    public function hasRequestFailed(Response $response): ?bool
    {
        return $response->json('code') !== 1;
    }

    public function getGroupResource(): GroupResource
    {
        return new GroupResource($this);
    }

    public function getChainResource(): ChainResource
    {
        return new ChainResource($this);
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
            'Host' => 'openapi.aifabu.com',
            'Origin' => 'https://openapi.aifabu.com',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];
    }
}
