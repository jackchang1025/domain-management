<?php

namespace App\Services\Integrations\Forage\Data;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;
class ActiveList extends Data
{
    public function __construct(
        public string $count,
        #[DataCollectionOf(Active::class)]
        public DataCollection $list,
    ) {}
}
