<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckDomains extends Command
{
    protected $signature = 'domains:check';
    protected $description = '检查有效域名并更新状态';

    public function handle()
    {
        // 先获取需要检测的域名数量
        $total = Domain::where('status', 'active')->count();
        
        // 动态计算锁定时间（25秒/个 × 安全系数1.2）
        $lockSeconds = min(max($total * 25 * 1.2, 60), 86400); // 限制在1分钟到24小时之间
        
        $lock = Cache::lock('domain_check_lock', $lockSeconds);

        if (!$lock->get()) {
            $this->error('检测任务已在运行中');
            return;
        }

        try {
            $apiKey = config('services.wx_check.key');
            
            Domain::where('status', 'active')
                ->chunkById(100, function ($domains) use ($apiKey) {
                    foreach ($domains as $domain) {
                        // 记录开始时间
                        $start = microtime(true);
                        
                        try {

                            $response = Http::timeout(15)
                                ->get('http://wx.rrbay.com/pro/wxUrlCheck2.ashx', [
                                    'key' => $apiKey,
                                    'url' => urlencode($domain->domain)
                                ]);

                                $this->info(now()->format('Y-m-d H:i:s') . " domain:" . $domain->domain . " result:" . $response->body());

                                Log::info("域名检测：{$domain->domain} body:{$response->body()}");

                                if($response->successful()){
                                    
                                    $result = $response->json();
                                
                                    if ($result['Code'] === '101') {
                                        $domain->update(['status' => 'expired']);
                                    }
                                }

                        } catch (\Exception $e) {
                            Log::error("域名检测异常：{$domain->domain}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        // 计算实际耗时并确保至少等待25秒
                        $elapsed = microtime(true) - $start;
                        $sleep = max(25 - $elapsed, 0); // 使用25秒作为最小间隔
                        if ($sleep > 0) {
                            sleep($sleep);
                        }
                    }
                });

            $this->info("域名检测完成，共耗时 {$lockSeconds} 秒");
        } finally {
            $lock->release();
        }
    }
} 