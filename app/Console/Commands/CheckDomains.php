<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Weijiajia\TencentUrlDetection\DriversManager;
use Psr\Log\LoggerInterface;
use Filament\Notifications\Notification;
use App\Models\User;

class CheckDomains extends Command
{
    protected $signature = 'domains:check';
    protected $description = '检查有效域名并更新状态';

    protected Lock $lock;

    protected bool $isLocked = false;

    public function handle(DriversManager $driversManager,LoggerInterface $logger): void
    {
        // 先获取需要检测的域名数量
        $total = Domain::where('status', 'active')->count();

        // 动态计算锁定时间（25秒/个 × 安全系数1.2）
        $lockSeconds = min(max($total * 5 * 1.2, 60), 86400); // 限制在1分钟到24小时之间

        $this->lock = Cache::lock('domain_check_lock', $lockSeconds);

        if (!$this->lock->get()) {
            $this->error('检测任务已在运行中');
            return;
        }

        $this->isLocked = true;

        try {

            Domain::where('status', 'active')
                ->chunkById(100, function ($domains) use ($driversManager,$logger) {
                    foreach ($domains as $domain) {
                        // 记录开始时间
                        $start = microtime(true);

                        try {

                                $response = $driversManager->forgetDrivers()
                                    ->driver()
                                    ->withLogger($logger)
                                    ->check($domain->domain);

                                $this->info(now()->format('Y-m-d H:i:s') . " domain:" . $domain->domain . " result:" . $response->getResponse()->body());

                                Log::info("域名检测：{$domain->domain} body:{$response->getResponse()->body()}");

                                if($response->isWeChatRiskWarning()){
                                    $domain->update(['status' => 'expired','wording_title' => $response->getWordingTitle(),'wording' => $response->getWording()]);

                                    Notification::make()
                                    ->title("{$domain->domain} 域名检测被拦截")
                                    ->body($response->getWording())
                                    ->warning()
                                    ->sendToDatabase(User::first());
                                }

                        } catch (\Exception $e) {
                            Log::error("域名检测异常：{$domain->domain}", [
                                'error' => $e->getMessage()
                            ]);

                            $this->error("域名检测异常：{$domain->domain} 错误信息：{$e->getMessage()}");
                        }

                        // 计算实际耗时并确保至少等待25秒
                        $elapsed = microtime(true) - $start;
                        $sleep = max(5 - $elapsed, 0); // 使用25秒作为最小间隔
                        if ($sleep > 0) {
                            sleep($sleep);
                        }
                    }
                });

            $this->info("域名检测完成，共耗时 {$lockSeconds} 秒");
        } finally {
            $this->lock->release();
        }
    }

    public function __destruct(){

        $this->isLocked && $this->lock->release();
    }
}
