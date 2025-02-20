<?php

namespace App\Services\Integrations\ShortUrl\Request;

use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Enums\Method;
use Saloon\Contracts\Body\HasBody;


class UpdateShortRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $code,
        public string $url,
    ) {
    }
    
    public function resolveEndpoint(): string
    {
        return '/dwz.php/my_dwz/dwz_update';
    }

    protected function defaultBody(): array
    {
        return [
            'dwz_url' => $this->url,
            'dwz_code' => $this->code,
        ];

    }
}