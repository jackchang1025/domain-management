<?php

namespace App\Services\Integrations\Aifabu\Request\Chain;

use Saloon\Http\Request;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use App\Services\Integrations\Aifabu\Data\Response\Chain\Chains;

class ListChainRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        //群组ID
        public int $groupId,
        //起始位置，默认为 0
        public int $start = 0,
        //请求的短链数量，不大于 1000，默认为 100
        public int $count = 100,
        //短链创建时间-起始日期（时间戳格式）
        public ?int $begin_date = null,
        //短链创建时间-结束日期（时间戳格式）
        public ?int $end_date = null,
    ) {

        if($this->count > 1000) {
            throw new \Exception('count must be less than 1000');
        }
        
    }

    public function createDtoFromResponse(Response $response): Chains
    {
        return Chains::from($response->json('result'));
    }

    public function resolveEndpoint(): string
    {
        return '/v1/chain/getChainList';
    }

    public function defaultQuery(): array
    {
        return [
            'group_id' => $this->groupId,
            'start' => $this->start,
            'count' => $this->count,
            'begin_date' => $this->begin_date,
            'end_date' => $this->end_date,
        ];
    }
}
