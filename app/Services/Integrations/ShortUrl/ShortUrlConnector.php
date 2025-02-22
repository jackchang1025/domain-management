<?php

namespace App\Services\Integrations\ShortUrl;

use App\Services\Integrations\ShortUrl\Request\ListShortUrlRequest;
use Saloon\Http\Connector;
use App\Services\Integrations\Trait\HasLogger;
use App\Services\Integrations\ShortUrl\Resource\Resource;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use App\Services\Integrations\ShortUrl\Pagination\ShortUrlPaginator;
use App\Services\Integrations\Trait\HasCookie;

class ShortUrlConnector extends Connector implements HasPagination
{
    use HasCookie;
    use HasLogger;

    public function resolveBaseUrl(): string
    {
        return 'https://hm.dw.googlefb.sbs';
    }

    public function getResource(): Resource
    {
        return new Resource($this);
    }

    public function paginate(\Saloon\Http\Request $request): ShortUrlPaginator
    {
        return new ShortUrlPaginator($this, $request);
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
            'Host' => 'hm.dw.googlefb.sbs',
            'Origin' => 'https://hm.dw.googlefb.sbs',
            'Referer' => 'https://hm.dw.googlefb.sbs',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];
    }
}
