<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\Redis;
use RuntimeException;

class DomainRedirectService
{
    public const QUEUE_KEY = 'domain_redirect_queue';

    public function getRedirectDomain(): string
    {
        $target = Redis::lpop(self::QUEUE_KEY);

        if (!$target) {
            $this->refillQueue();
            $target = Redis::lpop(self::QUEUE_KEY) ?: '';
        }

        if (empty($target)) {
            throw new RuntimeException('没有可用的域名');
        }

        return $target;
    }

    public function refillQueue(): void
    {
        $domains = Domain::with('group')
            ->where('status', 'active')
            ->orderBy('group_id')
            ->orderBy('id')
            ->pluck('domain');

        if ($domains->isEmpty()) {
            throw new RuntimeException('没有活跃的域名可用');
        }

        Redis::pipeline(function ($pipe) use ($domains) {
            $pipe->del(self::QUEUE_KEY);
            foreach ($domains as $domain) {
                $pipe->rpush(self::QUEUE_KEY, $domain);
            }
        });
    }
} 