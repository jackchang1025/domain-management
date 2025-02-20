<?php

namespace App\Services\Integrations\ShortUrl\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class ShortUrlList extends Data
{
    public function __construct(
        #[DataCollectionOf(ShortUrl::class)]
        public DataCollection $shortUrls,
    ) {
    }
}