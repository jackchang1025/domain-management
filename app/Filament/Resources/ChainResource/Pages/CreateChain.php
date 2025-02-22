<?php

namespace App\Filament\Resources\ChainResource\Pages;

use App\Filament\Resources\ChainResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Services\AifabuService;

class CreateChain extends CreateRecord
{
    protected static string $resource = ChainResource::class;

    protected static ?string $title = '创建短链';


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            //string $targetUrl, int $groupId, string $domain = null, string $chainTitle = null, string $validTime = null

            $service = app(AifabuService::class);
            // 1. 先执行API创建
            $response = $service->getAifabuConnector()
                ->getChainResource()
                ->create($data['target_url'], $data['group_id'] ?? null, $data['domain'] ?? null, $data['chain_title'] ?? null, $data['valid_time'] ?? null);

            if (!$response->successful() || empty($response->json('result'))) {
                throw new \Exception('API调用失败：'.$response->body());
            }

            // 使用parse_url获取URL路径部分
            $render_url = $response->json('result.render_url');
            $chain = parse_url($render_url, PHP_URL_PATH);

            // 3. 返回新创建的本机记录
            return [
                'chain_title' => $response->json('result.chain_title'),
                'target_url' => $data['target_url'],
                'render_url' => $render_url,
                'chain' => $chain,
                'group_id' => $response->json('result.group_id'),
                'domain' => $data['domain'],
                'valid_time' => $data['valid_time'],
            ];

        } catch (\Exception $e) {

            Notification::make()
                ->title('创建失败：'.$e->getMessage())
                ->danger()
                ->send();

            $this->halt();

        }
    }
}
