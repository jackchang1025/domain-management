<?php

namespace App\Services\Integrations\Forage\Request\Api\VisitorAnalysis;

use Saloon\Http\Request;
use Saloon\Enums\Method;

class GetCvidRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/visitor_analysis/get_cvid';
    }
}