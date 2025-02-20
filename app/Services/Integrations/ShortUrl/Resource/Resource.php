<?php

namespace App\Services\Integrations\ShortUrl\Resource;

use App\Services\Integrations\ShortUrl\ShortUrlConnector;
use App\Services\Integrations\ShortUrl\Request\LoginRequest;
use Saloon\Http\Response;
use App\Services\Integrations\ShortUrl\Request\CreateShortUrlRequest;
use App\Services\Integrations\ShortUrl\Request\ListShortUrlRequest;
use App\Services\Integrations\ShortUrl\Request\HomeRequest;
use App\Services\Integrations\ShortUrl\Request\UpdateShortRequest;

class Resource
{
    public function __construct(
        protected ShortUrlConnector $connector,
    ) {
        //
    }

    public function getConnector(): ShortUrlConnector
    {
        return $this->connector;
    }

    public function login(string $username, string $password): Response
    {
        return $this->connector->send(new LoginRequest($username, $password));
    }

    public function createShortUrl(string $url): Response
    {
        return $this->connector->send(new CreateShortUrlRequest($url));
    }

    public function listShortUrl(int $page = 1, string $duan, string $chang): Response
    {
        return $this->connector->send(new ListShortUrlRequest($page, $duan, $chang));
    }

    public function home(): Response
    {
        return $this->connector->send(new HomeRequest());
    }

    public function updateShortUrl(string $code, string $url): Response
    {
        return $this->connector->send(new UpdateShortRequest($code, $url));
    }
}
