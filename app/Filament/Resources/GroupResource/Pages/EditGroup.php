<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('删除分组')
                ->modalHeading('删除分组')
                ->modalDescription('确定要删除这个分组吗？删除后不可恢复。')
                ->successNotificationTitle('分组已删除'),
        ];
    }

    // 保存成功后跳转到列表页
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 