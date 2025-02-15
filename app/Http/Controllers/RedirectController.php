<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RedirectController extends Controller
{
    public const QUEUE_KEY = 'domain_redirect_queue';

    public function redirectDomain(): \Illuminate\Http\RedirectResponse
    {
        // 从队列中获取域名
        $target = Redis::lpop(self::QUEUE_KEY);

        // 如果队列为空，重新填充
        if (!$target) {
            // 按分组获取所有活跃域名
            $domains = Domain::query()
                ->with('group') // 预加载分组信息
                ->where('status', 'active')
                ->orderBy('group_id') // 首先按分组排序
                ->orderBy('id') // 然后按域名ID排序
                ->pluck('domain')
                ->toArray();

            if (empty($domains)) {
                abort(404, '没有可用的域名');
            }

            // 使用管道批量添加域名到队列
            Redis::pipeline(function ($pipe) use ($domains) {
                foreach ($domains as $domain) {
                    $pipe->rpush(self::QUEUE_KEY, $domain);
                }
            });

            // 重新获取域名
            $target = Redis::lpop(self::QUEUE_KEY);
        }

        $prefix = Str::random(6);
        return redirect()->away("http://{$prefix}.{$target}");
    }

}
