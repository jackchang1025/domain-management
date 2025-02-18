<?php

namespace App\Services\Integrations\Forage\Resource;

use App\Services\Integrations\Forage\ForageConnector;
use Saloon\Http\Response;
use App\Services\Integrations\Forage\Request\User\Active\ListRequest;
use App\Services\Integrations\Forage\Request\Api\QrCode\SaveActiveRequest;
use App\Services\Integrations\Forage\Data\SaveActive;

readonly class DomainResource
{
    public function __construct(protected ForageConnector $connector)
    {
        //
    }

    public function getConnector(): ForageConnector
    {
        return $this->connector;
    }

    /**
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function list(): Response
    {
        return $this->connector->send(new ListRequest());
    }

    /**
     * @param SaveActive $saveActive
     * @return Response
     * @throws \Saloon\Exceptions\Request\FatalRequestException
     * @throws \Saloon\Exceptions\Request\RequestException
     */
    public function saveActive(SaveActive $saveActive): Response
    {
        return $this->connector->send(new SaveActiveRequest($saveActive));
    }
}
