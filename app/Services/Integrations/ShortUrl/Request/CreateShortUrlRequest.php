<?php

namespace App\Services\Integrations\ShortUrl\Request;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Contracts\Body\HasBody;

class CreateShortUrlRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $url,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/dwz.php/index/insert_dwz';
    }

    protected function defaultBody(): array
    {
        return [
            'dwz_url' => $this->url,
        ];
    }
}