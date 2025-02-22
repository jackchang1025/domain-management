<?php

namespace App\Filament\Resources;

use App\Models\Chain;
use App\Services\Integrations\Aifabu\Enums\ChainType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\ChainResource\Pages;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use App\Services\AifabuService;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification as FilamentNotification;

class ChainResource extends Resource
{
    protected static ?string $model = Chain::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = '链接管理';
    protected static ?string $navigationGroup = '爱链接';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('chain_title')
                    ->label('链接标题')
                    ->helperText('链接标题，不填则默认为未命名')
                    ->maxLength(255),

                Forms\Components\Select::make('group_id')
                    ->relationship('group', 'group_name')
                    ->label('所属分组')
                    ->searchable()
                    ->preload()
                    ->helperText('所属分组，不填则默认使用账号的首个分组'),

                // Forms\Components\TextInput::make('domain')
                //     ->numeric()
                //     ->label('专属域名'),

                Forms\Components\TextInput::make('target_url')
                    ->label('目标网址')
                    ->required()
                    ->helperText('必须以 http:// 或 https:// 开头的链接')
                    ->url()
                    ->maxLength(2048),

                //valid_time 有效期
                Forms\Components\DateTimePicker::make('valid_time')
                    ->label('有效期')
                    ->helperText('有效期为空时，链接永久有效')
                    ->native(false),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('chain')
                    ->label('链接后缀')
                    ->searchable(),

                Tables\Columns\TextColumn::make('group.group_name')
                    ->label('所属分组')
                    ->sortable()
                    ->placeholder('未分组')
                    ->searchable(),

                Tables\Columns\TextColumn::make('target_url')
                    ->label('目标网址'),

                Tables\Columns\TextColumn::make('chain_title')
                    ->label('链接标题')
                    ->limit(30),

                Tables\Columns\TextColumn::make('render_url')
                    ->label('短链接网址'),

                Tables\Columns\TextColumn::make('domain_url')
                    ->label('专属域名'),

                Tables\Columns\TextColumn::make('domain_status')
                    ->label('域名状态')
                    ->badge(),


                Tables\Columns\TextColumn::make('type')
                    ->label('链接类型')
                    ->formatStateUsing(fn (ChainType $state) => $state->label()),

                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge(),

                Tables\Columns\TextColumn::make('pv_today')
                    ->label('今日访问')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pv_history')
                    ->label('历史访问')
                    ->numeric()
                    ->sortable(),


            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->relationship('group', 'group_name')
                    ->label('分组'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => '激活',
                        'inactive' => '未激活',
                        'archived' => '已归档'
                    ])
                    ->label('状态'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Model $record) {

                        try {

                            $service = app(AifabuService::class);
                            $response = $service->getAifabuConnector()
                                ->getChainResource()
                                ->delete([$record->chain]);

                            if (!$response->successful()) {
                                throw new \Exception("API删除失败：".$response->body());
                            }

                            return true;
                        } catch (\Exception $e) {
                            FilamentNotification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();

                            $action->halt();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {

                            try {

                                $service = app(AifabuService::class);

                                $response = $service->getAifabuConnector()
                                    ->getChainResource()
                                    ->delete($records->pluck('chain')->toArray());


                                if (!$response->successful()) {
                                    throw new \Exception("API删除失败：".$response->body());
                                }

                                return true;
                            }catch (\Exception $e) {

                                Notification::make()
                                    ->title("删除失败")
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();

                                $action->halt();
                            }
                        })
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChains::route('/'),
            'create' => Pages\CreateChain::route('/create'),
            'edit' => Pages\EditChain::route('/{record}/edit'),
        ];
    }
}
