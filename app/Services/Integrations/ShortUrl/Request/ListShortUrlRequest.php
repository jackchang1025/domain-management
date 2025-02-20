<?php

namespace App\Services\Integrations\ShortUrl\Request;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class ListShortUrlRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(
        public int $page = 1,
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
            'p' => $this->page,
            'duan' => $this->duan,
            'chang' => $this->chang,
        ];
    }
}
