<?php

namespace App\Services\Integrations\ShortUrl\Request;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ListShortUrlRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        public string $duan = '',
        public string $chang = '',
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/dwz.php/my_dwz/index";
    }

    protected function defaultQuery(): array
    {
        return [
            'duan' => $this->duan,
            'chang' => $this->chang,
        ];
    }
}
