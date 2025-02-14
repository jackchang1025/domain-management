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
                
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => '正常',
                        'expired' => '过期',
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
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('状态')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => '正常',
                        'expired' => '过期',
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
                        'expired' => '过期',
                    ])
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