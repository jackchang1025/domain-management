<?php

namespace App\Services;

use App\Models\{Domain, Chain};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * 爱发部链接更新服务
 *
 * 主要职责：
 * - 验证系统更新所需的前置条件
 * - 同步最新的群组和链接数据
 * - 将过期域名下的链接迁移到活跃域名
 * - 记录更新操作结果
 */
class AifabuUpdater
{
    /**
     * 已更新的链接集合
     * @var Collection<Chain>
     */
    protected Collection $updatedChains;

    /**
     * 当前活跃域名实例
     * @var Domain|null
     */
    protected ?Domain $activeDomain = null;

    public function __construct(
        private readonly AifabuService $service,
        private readonly LoggerInterface $logger
    ) {
        $this->updatedChains = new Collection();
    }

    /**
     * 执行完整更新流程
     *
     * @return Collection<Chain> 更新成功的链接集合
     * @throws \RuntimeException 当基础验证失败时抛出
     */
    public function execute(): Collection
    {
        $this->validatePrerequisites();

        $this->service->syncGroupChains();

        $this->processDomains();

        $this->logger->info('链接更新完成', [
            'updated_count' => $this->updatedChains->count(),
            'active_domain' => $this->activeDomain?->domain
        ]);

        return $this->updatedChains;
    }

    /**
     * 验证系统前置条件
     *
     * @throws \RuntimeException
     */
    private function validatePrerequisites(): void
    {
        if (!Domain::where('status', 'active')->exists()) {
            throw new \RuntimeException('系统缺少正常状态的域名');
        }
    }

    /**
     * 获取当前活跃域名
     *
     * @return Domain
     * @throws ModelNotFoundException
     */
    protected function getActiveDomain(): Domain
    {
        return $this->activeDomain ??= Domain::where('status', 'active')->whereNotNull('domain')->firstOrFail();
    }

    /**
     * 获取带协议前缀的新域名
     */
    protected function getNewProtocolDomain(): string
    {
        return DomainComparator::ensureProtocol($this->getActiveDomain()->domain);
    }

    /**
     * 更新单个链接到新域名
     *
     * @param Chain $chain 需要更新的链接
     * @param string $newDomain 带协议的新域名
     * @return Chain|null 更新成功返回实例，失败返回null
     */
    protected function updateChain(Chain $chain, string $newDomain): ?Chain
    {
        try {

            $response = $this->service->getAifabuConnector()
                ->getChainResource()
                ->update(
                    $chain->chain,
                    $newDomain,
                    $chain->chain_title
                );


            if ($response->successful() && $response->json('result.render_url') && $response->json('result.target_url')) {
                $chain->update([
                    'render_url' => $response->json('result.render_url'),
                    'target_url' => $response->json('result.target_url'),
                ]);

                $this->logger->info('链接更新成功', [
                    'chain' => $chain->chain,
                    'new_domain' => $newDomain
                ]);

                return $chain;
            }

            $this->logger->error('API请求失败', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

        } catch (\Throwable $e) {

            $this->logger->error('更新链接时发生异常', [
                'chain' => $chain->chain,
                'error' => $e->getMessage()
            ]);
        }
        return null;
    }

    /**
     * 获取需要处理的链接集合（带预加载）
     */
    protected function getChainsToProcess(): Collection
    {
        return Chain::whereNotNull('target_url')->get();
    }

    /**
     * 获取过期域名列表（仅需domain字段）
     */
    protected function getExpiredDomains(): Collection
    {
        return Domain::where('status', 'expired')->get();
    }

    /**
     * 处理域名更新流程
     */
    protected function processDomains(): void
    {
        $expiredDomains = $this->getExpiredDomains();
        $chains = $this->getChainsToProcess();


        $expiredDomains->each(function (Domain $expiredDomain) use ($chains) {

            $chains->filter(fn(Chain $chain) => $chain->equalsDomain($expiredDomain->domain))->each(function (Chain $chain) {

                $response = $this->updateChain(
                    $chain,
                    $this->getNewProtocolDomain()
                );

                if ($response) {
                    $this->updatedChains->push($response);
                }
            });
        });
    }
}
