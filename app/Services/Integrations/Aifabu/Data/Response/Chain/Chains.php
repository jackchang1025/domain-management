<?php

namespace App\Services\Integrations\Aifabu\Data\Response\Chain;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class Chains extends Data
{
    /**
     * total integer 总条数 
     * list_num integer 每页数据条数 
     * list array 数据列表 
     */
    public function __construct(
        #[DataCollectionOf(Chain::class)]
        public ?DataCollection $list = null,
        public int $total = 0,
        public int $list_num = 0,
    ) {
    }
}