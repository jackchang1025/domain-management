<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainResource\Pages;
use App\Models\Domain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationLabel = '域名管理';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('domain')
                    ->label('域名')
                    ->required()
                    ->placeholder('请输入域名，每行一个
例如：
example1.com
example2.com
example3.com')
                    ->helperText('每行输入一个域名')
                    ->rows(10)
                    ->columnSpanFull()
                    ->afterStateUpdated(function (string $state, Forms\Set $set) {
                        // 清理输入，移除空行和重复行
                        $domains = collect(explode("\n", $state))
                            ->map(fn ($domain) => trim($domain))
                            ->filter()
                            ->unique()
                            ->values()
                            ->join("\n");
                        
                        $set('domain', $domains);
                    }),
                
                Forms\Components\Select::make('group_id')
                    ->label('所属分组')
                    ->relationship(
                        name: 'group',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->orderBy('name')
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('分组名称')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('描述')
                            ->maxLength(255),
                    ])
                    ->createOptionAction(
                        function (Forms\Components\Actions\Action $action) {
                            return $action
                                ->modalHeading('创建新分组')
                                ->modalButton('创建分组')
                                ->modalWidth('md');
                        }
                    ),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => '正常',
                        'expired' => '拦截',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('group.name')
                    ->label('所属分组')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('状态')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '正常',
                        'expired' => '拦截',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'expired' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'active' => '正常',
                        'expired' => '拦截',
                    ])
                ,
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('分组')
                    ->relationship('group', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
} 