<?php

namespace App\Services\Integrations\Forage\Request\Login;

use Saloon\Contracts\Body\HasBody;
use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;

class EntryRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $account,
        public string $password,
        public string $captchaTicket = '',
        public string $captchaRandStr = '',
        public string $openId = '',
        public string $time = '',
        public string $publicKey = '',
        public string $isDemoAccount = '0',
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/login/entry';
    }

    protected function defaultBody(): array
    {
        return [
            'captcha_ticket'   => $this->captchaTicket,
            'captcha_rand_str' => $this->captchaRandStr,
            'account'          => $this->account,
            'open_id'          => $this->openId,
            'time'             => $this->time,
            'publickey'        => $this->publicKey,
            'is_demo_account'  => $this->isDemoAccount,
            'password'         => $this->password,
        ];
    }

}
