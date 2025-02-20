<?php

namespace App\Filament\Resources\ShortUrlResource\Pages;

use App\Filament\Resources\ShortUrlResource;
use App\Services\ShortUrlService;
use Filament\Resources\Pages\CreateRecord;
use App\Models\ShortUrl;
use Filament\Notifications\Notification;

class CreateShortUrl extends CreateRecord
{
    protected static string $resource = ShortUrlResource::class;

    protected function handleRecordCreation(array $data): ShortUrl
    {
        try {
            $service = app(ShortUrlService::class);
            
            // 1. 先执行API创建
            $response = $service->createShortUrl($data['long_url']);
            
            if (!$response->successful() || $response->json('status') !== 1) {
                throw new \Exception('API调用失败：'.$response->body());
            }
            
            // 2. 单独执行同步
            $service->syncDataFromApi();
            
            // 3. 返回新创建的本机记录
            return ShortUrl::latest()->first();
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('创建失败：'.$e->getMessage())
                ->danger()
                ->send();
            
            throw $e; // 中断创建流程
        }
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
