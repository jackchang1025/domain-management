<?php

namespace App\Services\Integrations\Forage;

use Saloon\Http\PendingRequest;
use GuzzleHttp\Cookie\CookieJarInterface;
use GuzzleHttp\Cookie\CookieJar;

trait HasCookie
{
    protected ?CookieJarInterface $cookieJar = null;

    public function bootHasCookie(PendingRequest $pendingRequest): void
    {
        $pendingRequest->getConnector()->config()->add('cookies', $this->cookieJar);
    }

    public function withCookies(CookieJarInterface|array|null $cookies, bool $strictMode = false): static
    {
        if (is_array($cookies)) {

            $this->cookieJar = new CookieJar($strictMode, $cookies);
        } elseif ($cookies instanceof CookieJarInterface) {

            $this->cookieJar = $cookies;
        } else {

            $this->cookieJar = null;
        }

        return $this;
    }

    public function getCookieJar(): ?CookieJarInterface
    {
        return $this->cookieJar;
    }
}
