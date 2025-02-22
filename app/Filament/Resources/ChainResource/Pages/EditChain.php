<?php

namespace App\Filament\Resources\ChainResource\Pages;

use App\Filament\Resources\ChainResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Services\AifabuService;
use Filament\Forms\Form;
use Filament\Forms;
class EditChain extends EditRecord
{
    protected static string $resource = ChainResource::class;

    protected static ?string $title = '修改短链';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public  function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('chain_title')
                    ->label('链接标题')
                    ->helperText('链接标题，不填则默认为未命名')
                    ->maxLength(255),

                    // chain 字段不允许修改
                Forms\Components\TextInput::make('chain')
                    ->label('链接后缀')
                    ->maxLength(255)
                    ->disabled(),

                Forms\Components\TextInput::make('target_url')
                    ->label('目标网址')
                    ->required()
                    ->helperText('必须以 http:// 或 https:// 开头的链接')
                    ->url()
                    ->maxLength(2048),

            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {

            $service = app(AifabuService::class);

            $response = $service->getAifabuConnector()
                ->getChainResource()
                ->update($this->getRecord()->chain, $data['target_url'], $data['chain_title']);

            if (!$response->successful() || empty($response->json('result'))) {
                throw new \Exception('API调用失败：'.$response->body());
            }

            return [
                'target_url' => $response->json('result.target_url'),
                'render_url' => $response->json('result.render_url'),
                'chain_title' => $data['chain_title'],
            ];

        }catch (\Exception $e) {

            Notification::make()
                ->title('创建失败：'.$e->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }
}
