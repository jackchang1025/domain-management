<?php

namespace App\Services\Integrations\Forage\Request\Api\QrCode;


use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Contracts\Body\HasBody;
use App\Services\Integrations\Forage\Data\SaveActive;
class SaveActiveRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(protected SaveActive $saveActive){}

    public function resolveEndpoint(): string
    {
        return "https://cli.im/Apis/QrCode/saveActive";
    }

    protected function defaultBody(): array
    {
        return $this->saveActive->toArray();
    }

    protected function defaultHeaders(): array
    {

        return [
            'Host' => 'cli.im',
            'Origin' => 'https://cli.im',
            'Pragma' => 'no-cache',
            'Referer' => 'https://cli.im/user/active/edit/47130006?p=1&t=jump&i=1',
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
}
