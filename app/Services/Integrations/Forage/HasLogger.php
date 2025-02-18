<?php

namespace App\Services\Integrations\Forage;
use Psr\Log\LoggerInterface;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Saloon\Enums\PipeOrder;

trait HasLogger
{
    protected bool $debug = false;

    protected ?LoggerInterface $logger = null;

    public function withLogger(?LoggerInterface $logger = null): static
    {
        $this->logger = $logger;

        return $this;
    }

    public function bootHasLogger(PendingRequest $pendingRequest): void
    {
        if ($this->debug) {
            return;
        }

        $this->debug = true;
        
        $pendingRequest->getConnector()
                ->middleware()
                ->onRequest(
                    fn(PendingRequest $request) => $this->formatRequestLog($request),
                    'logger_request',
                    PipeOrder::LAST
                );

        $pendingRequest->getConnector()
                ->middleware()
                ->onResponse(
                    fn(Response $response) => $this->formatResponseLog($response),
                    'logger_response',
                    PipeOrder::FIRST
                );
    }

    protected function formatRequestLog(PendingRequest $request): ?PendingRequest
    {
        $this->getLogger()?->debug('request', [
            'method'  => $request->getMethod(),
            'uri'     => (string)$request->getUri(),
            'headers' => $request->headers(),
            'cookies' => $request->config()->get('cookies')?->toArray(),
            'body'    => (string)$request->body(),
        ]);

        return $request;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    protected function formatResponseLog(Response $response): ?Response
    {
        $this->getLogger()?->debug('response', [
            'status'  => $response->status(),
            'headers' => $response->headers(),
            'body'    => $response->body(),
        ]);

        return $response;
    }
}