<?php

namespace App\Console\Commands;

use App\Services\Integrations\Forage\Data\Active;
use Exception;
use Illuminate\Console\Command;
use App\Services\ForageService;
use App\Services\Integrations\Forage\Data\SaveActive;
use App\Models\Domain;
use Illuminate\Support\Facades\Log;
use App\Services\DomainComparator;
use JsonException;
use RuntimeException;
use App\Services\Integrations\Forage\Data\ActiveList;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

class Forage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:forage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新屏蔽域名的跳转链接';

    /**
     * Execute the console command.
     * @return void
     * @throws FatalRequestException
     * @throws JsonException
     * @throws RequestException
     */
    public function handle(): void
    {
        try {
            // 验证配置
            $this->validateConfig();

            // 初始化服务
            $forageService = $this->initForageService();

            // 获取需要处理的域名
            $expiredDomains = $this->getExpiredDomains();

            // 获取正常域名
            $activeDomain   = $this->getActiveDomain();

            // 获取活跃域名列表
            $activeList = $this->getActiveList($forageService);

            // 处理每个屏蔽域名
            $this->processDomains($expiredDomains, $activeList, $forageService, $activeDomain);

            $this->handleLog('域名更新完成');
        } catch (RuntimeException $e) {
            $this->handleLog("操作失败: {$e->getMessage()}", 'error');
        }
    }

    private function validateConfig(): void
    {
        if (!config('forage.account') || !config('forage.password')) {
            throw new RuntimeException('Forage配置信息不完整');
        }
    }

    private function initForageService(): ForageService
    {
        $account    = config('forage.account');
        $password   = config('forage.password');
        $cookieFile = config('forage.cookie_file', storage_path("app/public/{$account}.json"));

        return new ForageService(
            account: $account,
            password: $password,
            cookieFile: $cookieFile
        );
    }

    private function getExpiredDomains()
    {
        $domains = Domain::where('status', 'expired')->get();
        if ($domains->isEmpty()) {
            throw new RuntimeException('没有需要处理的屏蔽域名');
        }

        return $domains;
    }

    private function getActiveDomain(): Domain
    {
        $domain = Domain::where('status', 'active')->first();
        if (!$domain) {
            throw new RuntimeException('系统中没有可用的正常域名');
        }

        return $domain;
    }

    /**
     * @throws FatalRequestException
     * @throws RequestException
     * @throws JsonException
     */
    private function getActiveList(ForageService $service): ActiveList
    {
        try {

            return $service->domainList();
        } catch (JsonException $e) {
            $this->handleLog('检测到登录失效，尝试重新登录...');
            $service->login();

            return $service->domainList();
        }
    }

    private function handleLog(string $message, string $level = 'info'): void
    {
        $formatted = "[Forage] {$message}";
        Log::$level($formatted);
        $this->{$level === 'error' ? 'error' : 'info'}($formatted);
    }

    private function processDomains($domains, $activeList, ForageService $service, Domain $activeDomain): void
    {
        foreach ($domains as $domain) {
            $this->processSingleDomain($domain, $activeList, $service, $activeDomain);
        }
    }

    private function processSingleDomain(Domain $domain, ActiveList $activeList, ForageService $service, Domain $activeDomain): void
    {
        $activeList->list->toCollection()
            ->filter(function (Active $active) use ($domain) {
                return DomainComparator::equals($active->jumpUrl, $domain->domain);
            })
            ->each(function (Active $active) use ($service, $activeDomain) {
                $this->updateActiveRecord($active, $service, $activeDomain);
            });
    }

    private function updateActiveRecord(Active $active, ForageService $service, Domain $domain): void
    {
        try {

            $domainUrl = DomainComparator::ensureProtocol($domain->domain);

            $service->saveActive(SaveActive::from([
                'url'      => $domainUrl,
                'type'     => $active->type,
                'add_from' => $active->add_from,
                'note'     => $active->note,
                'activeid' => $active->id,
                'coding'   => $active->coding,
                'is_check' => $active->is_check,
                'is_del'   => $active->is_del,
                'ystype'   => $active->type,
                'active_list_id' => $active->active_list_id,
            ]));

            $this->handleLog("成功更新记录: {$domainUrl}");
        } catch (Exception $e) {
            $this->handleLog("更新记录失败: {$e->getMessage()}", 'error');
        }
    }
}
