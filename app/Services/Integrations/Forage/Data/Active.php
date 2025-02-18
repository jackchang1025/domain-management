<?php

namespace App\Services\Integrations\Forage\Data;
use Spatie\LaravelData\Data;

class Active extends Data
{
    public function __construct(
        public string $id,
        public string $note,
        public string $add_time,
        public string $entrurl,
        public string $user_id,
        public string $coding = '',
        public string $type = '',
        public string $is_check = '',
        public string $is_del = '',
        public string $is_share = '',
        public string $is_hold = '',
        public string $is_content = '',
        public string $lastup_ip = '',
        public string $ip_address = '',
        public string $lastup_time = '',
        public string $api_id = '',
        public string $routing_guid = '',
        public string $active_list_id = '',
        public string $scan_sum = '',
        public string $scene_id = '',
        public string $add_from = '',
        public string $is_link = '',
        public string $cli_batch_id = '',
        public string $is_state = '',
        public string $desc = '',
        public string $typecss = '',
        public string $typename = '',
        public string $jumpUrl = '',
        
        
    ){}
}
