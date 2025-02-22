<?php

namespace App\Services\Integrations\Aifabu\Data\Response\Group;

use Spatie\LaravelData\Data;

class Group extends Data
{
    public function __construct(
        public int $group_id,
        public string $group_name,
        public int $chain_num,
    ) {}
}