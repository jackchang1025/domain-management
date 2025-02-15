<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    // 创建成功后跳转到列表页
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 