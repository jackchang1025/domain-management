<?php

namespace App\Services\Integrations\ShortUrl\Data;

use Spatie\LaravelData\Data;

class ShortUrl extends Data
{
    public function __construct(
        public string $short_url,
        public string $long_url,
        public int $visit_count,
        public string $code,
        public string $edit_link,
    ) {}
}