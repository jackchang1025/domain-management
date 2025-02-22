<?php

namespace App\Services\Integrations\Aifabu\Data\Response\Chain;

use Spatie\LaravelData\Data;

class Chain extends Data
{
    /**
     *  chain_title string 链接标题 可选
     *  domain integer 域名id
     *  target_url string 跳转目标网址 可选
     *  status string 状态 可选
     *  create_time string 创建时间 可选
     *  pv_history integer 历史访问次数 可选
     *  pv_today integer 今日访问次数 可选
     *  chain string 链接后缀（唯一值） 可选
     *  domain_url string 域名 可选
     *  domain_status integer 域名状态(1：已生效 99：未生效) 可选
     *  type integer 链接类型 可选
     *  sub_type integer 链接子类型 可选
     *  render_url string 短连接网址 可选
     */
    
    public function __construct(
        public string $chain_title,
        public int $domain,
        public string $target_url,
        public string $status,
        public string $create_time,
        public int $pv_history,
        public int $pv_today,
        public string $chain,
        public string $domain_url,
        public int $domain_status,
        public int $type,
        public int $sub_type,
        public string $render_url,
    ) {
        //
    }
}