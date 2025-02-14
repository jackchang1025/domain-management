<?php

namespace App\Filament\Resources\DomainResource\Pages;

use App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // 将域名文本分割为数组
        $domains = collect(explode("\n", $data['domain']))
            ->map(fn ($domain) => trim($domain))
            ->filter()
            ->unique()
            ->values();

        // 开始事务
        return DB::transaction(function () use ($domains, $data) {
            // 创建第一个域名记录并返回
            $firstDomain = static::getModel()::create([
                'domain' => $domains->first(),
                'status' => $data['status'],
            ]);

            // 创建其余域名记录
            $domains->slice(1)->each(function ($domain) use ($data) {
                static::getModel()::create([
                    'domain' => $domain,
                    'status' => $data['status'],
                ]);
            });

            return $firstDomain;
        });
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
