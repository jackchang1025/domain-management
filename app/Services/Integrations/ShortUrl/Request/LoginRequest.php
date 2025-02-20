<?php

namespace App\Services\Integrations\ShortUrl\Request;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Contracts\Body\HasBody;
class LoginRequest extends Request  implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public string $username,
        public string $password,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/dwz.php/user/check_login';
    }

    protected function defaultBody(): array
    {
        return [
            'login_name' => $this->username,
            'login_passwd' => $this->password,
        ];
    }
}