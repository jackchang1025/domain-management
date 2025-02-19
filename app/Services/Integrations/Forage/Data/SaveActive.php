<?php

namespace App\Services\Integrations\Forage\Data;

use Spatie\LaravelData\Data;

class SaveActive extends Data
{

    public function __construct(
        public string $activeid,
        public string $url,
        public string $add_from,
        public string $note,
        public string $coding,
        public string $front_add = '1',
        public string $type = 'jump',
        public string $isadd = '1',
        public int $is_check = 0,
        public string $is_del = '0',
        public string $ystype = 'jump',
        public string $refresh_qr = '1',
        public string $active_list_id = '1',
        public string $wxshare_title = '',
        public string $key = '',
        public int|string $time = '',
        public array $content = []
    ) {
        $this->content = [
            0  => [
                'key'   => 'url',
                'value' => $this->url,  // Now accessible via $this
            ],
            22 => ['key' => 'tongji'],
            27 => ['key' => 'is_hold'],
            28 => ['key' => 'theme'],
        ];
    }
}
