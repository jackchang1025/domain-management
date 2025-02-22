<?php

namespace App\Services;

use App\Services\Integrations\Aifabu\AifabuConnector;
use Illuminate\Support\Facades\DB;
use App\Services\Integrations\Aifabu\Data\Response\Group\Groups;
use App\Services\Integrations\Aifabu\Data\Response\Group\Group;
use App\Services\Integrations\Aifabu\Data\Response\Chain\Chains;
use App\Services\Integrations\Aifabu\Data\Response\Chain\Chain as ChainData;
use App\Models\ChainGroup;
use App\Models\Chain;
use Psr\Log\LoggerInterface;

class AifabuService
{
    protected AifabuConnector $connector;

    public function __construct(
        protected string $token,
    ) {
        $this->connector = new AifabuConnector($token);
        $this->connector->withLogger(app(LoggerInterface::class));
    }

    public function getAifabuConnector(): AifabuConnector
    {
        return $this->connector;
    }

    public function syncGroup(): void
    {
        /** @var Groups $response */
        $response = $this->connector->getGroupResource()->getGroupList()->dto();

        if ($response->result === null || $response->result->count() === 0) {
            return;
        }

        // 仅数据库操作在事务中
        DB::transaction(function () use ($response) {
            // 批量更新或创建
            $response->result->toCollection()->each(function (Group $item) {
                ChainGroup::updateOrCreate(
                    ['group_id' => $item->group_id],
                    [
                        'group_name' => $item->group_name,
                    ]
                );
            });

            // 删除本地多余数据
            $groupIds = $response->result->toCollection()->pluck('group_id');

            ChainGroup::whereNotIn('group_id', $groupIds)->delete();
        });
    }

    public function syncChain(): void
    {

        $groupIds = ChainGroup::get()->pluck('group_id');


        foreach ($groupIds as $groupId) {

            /** @var Chains $response */
            $response = $this->connector->getChainResource()->list($groupId)->dto();


            if($response->list === null) {
                continue;
            }

            $collection = $response->list->toCollection();

            $collection->each(function (ChainData $item) use ($groupId) {


                Chain::updateOrCreate(
                    ['chain' => $item->chain],
                    [
                        'chain_title' => $item->chain_title,
                        'domain' => $item->domain,
                        'target_url' => $item->target_url,
                        // 'valid_time' => $item->valid_time,
                        'group_id' => $groupId,
                        'status' => $item->status,
                        'create_time' => $item->create_time,
                        'pv_history' => $item->pv_history,
                        'pv_today' => $item->pv_today,
                        'type' => $item->type,
                        'sub_type' => $item->sub_type,
                        'render_url' => $item->render_url,
                        'domain_url' => $item->domain_url,
                        'domain_status' => $item->domain_status,

                    ]
                );
            });

            Chain::where('group_id', $groupId)->whereNotIn('chain', $collection->pluck('chain'))->delete();
        }
    }

}
