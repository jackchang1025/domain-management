<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AifabuUpdater;
use App\Models\Chain;
use Illuminate\Support\Facades\Cache;

class UpdateAifabu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-aifabu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新爱链接';

    /**
     * Execute the console command.
     */
    public function handle(AifabuUpdater $updater): void
    {
        
        $lock = Cache::lock('update_aifabu_lock');

        if (!$lock->get()) {
            $this->error('更新任务已在运行中');
            return;
        }

        try {

            $result = $updater->execute();

            $result->each(function (Chain $chain) {
                $this->info('成功更新 '.$chain->chain.' 记录');
            });

            $this->info('成功更新 '.$result->count().' 条记录');
            $this->info('爱链接更新完成');
            
        } catch (\Exception $e) {

            $this->error('更新失败: '.$e->getMessage());
        } finally {
            
            $lock->release();
        }
        
        
    }
}
