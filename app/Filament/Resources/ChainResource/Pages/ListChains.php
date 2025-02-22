<?php

namespace App\Filament\Resources\ChainResource\Pages;

use App\Filament\Resources\ChainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Services\AifabuService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
class ListChains extends ListRecords
{
    protected static string $resource = ChainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('sync')
                    ->label('立即同步')
                    ->action(function (AifabuService $service) {
                        
                        try {
                            $service->syncChain();

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
                    }),
        ];
    }
} 