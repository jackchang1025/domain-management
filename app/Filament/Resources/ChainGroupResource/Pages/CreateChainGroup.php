<?php

namespace App\Filament\Resources\ChainGroupResource\Pages;

use App\Filament\Resources\ChainGroupResource;
use App\Services\AifabuService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Services\Integrations\Aifabu\Enums\ChainType;

class CreateChainGroup extends CreateRecord
{
    protected static string $resource = ChainGroupResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {

            $service = app(AifabuService::class);

            // 将字符串转换为ChainType枚举实例
            $chainType = ChainType::from($data['chain_type']);

            // 1. 先执行API创建
            $response = $service->getAifabuConnector()
            ->getGroupResource()
            ->createGroup($data['group_name'], $chainType);

            if (!$response->successful() || empty($response->json('result.group_id'))) {
                throw new \Exception('API调用失败：'.$response->body());
            }

            // 3. 返回新创建的本机记录
            return [
                'group_id' => $response->json('result.group_id'),
                'group_name' => $data['group_name'],
                'chain_type' => $data['chain_type'],
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
