<?php

namespace App\Services\Integrations\Forage\Request\User\Active;

use App\Services\Integrations\Forage\Data\ActiveList;
use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Contracts\Body\HasBody;

class ListRequest extends Request  implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public int $activeListId = 1,
        public string $t = 'web',
        public int $p = 1,
        public string $searchText = '',
        public int $l = 20,
    ) {
    }


    protected function defaultHeaders(): array
    {

        return [
            'Host' => 'cli.im',
            'Origin' => 'https://cli.im',
            'Pragma' => 'no-cache',
            'Referer' => 'https://cli.im/user/active?t=web&i=1&p=1&l=20',
            'Sec-Ch-Ua' => '"Not(A:Brand";v="99", "Google Chrome";v="133", "Chromium";v="133"',
            'Sec-Ch-Ua-Mobile' => '?0',
            'Sec-Ch-Ua-Platform' => '"Windows"',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'active_list_id' => $this->activeListId,
            't' => $this->t,
            'p' => $this->p,
            'search_text' => $this->searchText,
            'l' => $this->l,
        ];
    }

    public function createDtoFromResponse(Response $response): ActiveList
    {
        return ActiveList::from($response->json());
    }

    public function resolveEndpoint(): string
    {
        return 'https://cli.im/user/active/index';
    }


}
