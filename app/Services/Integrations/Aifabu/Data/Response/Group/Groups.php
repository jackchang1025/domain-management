<?php

namespace App\Services\Integrations\Aifabu\Data\Response\Group;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class Groups extends Data
{
    public function __construct(
        #[DataCollectionOf(Group::class)]
        public ?DataCollection $result = null,
    ) {}
}
