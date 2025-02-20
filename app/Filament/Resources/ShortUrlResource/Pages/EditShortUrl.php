<?php

namespace App\Filament\Resources\ShortUrlResource\Pages;

use App\Filament\Resources\ShortUrlResource;
use App\Models\ShortUrl;
use App\Services\ShortUrlService;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditShortUrl extends EditRecord
{
    protected static string $resource = ShortUrlResource::class;

    protected function handleRecordUpdate($record, array $data): ShortUrl
    {
        $service = app(ShortUrlService::class);
        
        $service->updateShortUrl($record->code, $data['long_url']);

        $record->update([
            'long_url' => $data['long_url'],
        ]);
        
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
