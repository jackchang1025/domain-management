<?php

namespace App\Filament\Resources\ChainGroupResource\Pages;

use App\Filament\Resources\ChainGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Services\AifabuService;
use Filament\Notifications\Notification;


class ListChainGroups extends ListRecords
{
    protected static string $resource = ChainGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('sync')
                    ->label('立即同步')
                    ->action(function (AifabuService $service) {
                        try {

                            $service->syncGroup();

                            Notification::make()
                                ->title('同步成功')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('同步失败：'.$e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
        ];
    }
} 