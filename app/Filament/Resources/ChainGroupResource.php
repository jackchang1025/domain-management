<?php

namespace App\Filament\Resources;

use App\Models\ChainGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\Integrations\Aifabu\Enums\ChainType;

class ChainGroupResource extends Resource
{
    protected static ?string $model = ChainGroup::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = '爱链接';
    protected static ?string $navigationLabel = '链接分组管理';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('group_name')
                    ->required()
                    ->maxLength(255)
                    ->label('分组名称'),
                    
                Forms\Components\Select::make('chain_type')
                    ->options(ChainType::getLabelValuesArray())
                    ->required()
                    ->label('链接类型')
                    ->native(false)
                    ->helperText('链接类型，不填则默认使用短链'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_id')
                    ->label('groupid')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('group_name')
                    ->label('分组名称')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('chain_type')
                    ->label('链接类型')
                    ->formatStateUsing(fn (ChainType $state) => $state->label()),
                    
            ])
            ->filters([
                // 添加过滤器
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ChainGroupResource\Pages\ListChainGroups::route('/'),
            'create' => \App\Filament\Resources\ChainGroupResource\Pages\CreateChainGroup::route('/create'),
            'edit' => \App\Filament\Resources\ChainGroupResource\Pages\EditChainGroup::route('/{record}/edit'),
        ];
    }
} 