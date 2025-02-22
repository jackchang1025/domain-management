<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShortUrlResource\Pages;
use App\Services\ShortUrlService;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Models\ShortUrl;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Domain;
use Filament\Forms\Components\Select;

class ShortUrlResource extends Resource
{
    protected static ?string $model = ShortUrl::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = '短链管理';

    protected static ?string $navigationGroup = '短链管理';

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->headerActions([
                
                Action::make('sync')
                    ->label('立即同步')
                    ->action(function (ShortUrlService $service) {
                        try {

                            $service->syncDataFromApi();

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
            ])
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('code')
                    ->label('code')
                    ->searchable(),

                TextColumn::make('short_url')
                    ->label('短链接')
                    ->searchable(),

                TextColumn::make('long_url')
                    ->label('原始链接')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('visit_count')
                    ->label('访问次数')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime(),

                TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime(),

            ])
            ->actions([
                EditAction::make(),
                // DeleteAction::make()
                //     ->before(function (ShortUrl $record) {
                //         $service = app(ShortUrlService::class);
                //         $service->deleteShortUrl($record->code);
                //     })
                //     ->after(function () {
                //         // 可选：添加删除后的回调
                //     }),
            ]);
    }
    
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([

            Select::make('long_url')
                ->label('长网址')
                ->options(Domain::all()->pluck('domain', 'domain'))
                ->searchable()
                ->required()
                ->hint('选择已有域名或输入新网址')
                ->hintIcon('heroicon-o-information-circle'),

            TextInput::make('short_url')
                ->disabled()
                ->label('短链代码')
                ->url(),
        ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShortUrls::route('/'),
            'create' => Pages\CreateShortUrl::route('/create'),
            'edit' => Pages\EditShortUrl::route('/{record}/edit'),
        ];
    }
} 