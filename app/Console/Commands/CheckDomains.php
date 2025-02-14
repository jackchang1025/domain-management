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
        
        // 动态计算锁定时间（20秒/个 × 安全系数1.2）
        $lockSeconds = min(max($total * 20 * 1.2, 60), 86400); // 限制在1秒到24小时之间
        
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

                            if ($response->successful()) {
                                $result = $response->json();
                                
                                $this->info("domain:".$domain->domain."result:".$response->body());

                                // 根据接口文档假设返回格式：{"status":1} 表示正常
                                if ($result['Code'] !== '102') {
                                    $domain->update(['status' => 'expired']);
                                    Log::info("域名检测失败：{$domain->domain}");
                                }

                            } else {
                                Log::error("接口请求失败：{$domain->domain}", [
                                    'status' => $response->status()
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error("域名检测异常：{$domain->domain}", [
                                'error' => $e->getMessage()
                            ]);
                        }
                        
                        // 计算实际耗时
                        $elapsed = microtime(true) - $start;
                        $sleep = max(20 - $elapsed, 0);
                        sleep($sleep); // 精确控制间隔
                    }
                });

            $this->info("域名检测完成，共耗时 {$lockSeconds} 秒");
        } finally {
            $lock->release();
        }
    }
} 