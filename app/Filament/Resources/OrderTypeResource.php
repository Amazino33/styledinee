<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderTypeResource\Pages;
use App\Models\OrderType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class OrderTypeResource extends Resource
{
    protected static ?string $model = OrderType::class;
    protected static null|BackedEnum|string $navigationIcon  = 'heroicon-o-tag';
    protected static null|UnitEnum|string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Order Types';
    protected static ?int    $navigationSort  = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(100),

            TextInput::make('slug')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->helperText('Lowercase, underscores only. e.g. ready_made'),

            TextInput::make('icon')
                ->maxLength(10)
                ->helperText('Optional emoji icon shown in POS'),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->label('Sort Order'),

            Toggle::make('needs_production')
                ->label('Requires Production')
                ->helperText('Goes through the production/tailoring pipeline')
                ->default(true),

            Toggle::make('needs_measurements')
                ->label('Requires Measurements')
                ->default(false),

            Toggle::make('needs_estimated_date')
                ->label('Show Estimated Completion Date')
                ->default(true),

            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')->label('')->width(40),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->badge()->color('gray'),
                Tables\Columns\IconColumn::make('needs_production')->boolean()->label('Production'),
                Tables\Columns\IconColumn::make('needs_measurements')->boolean()->label('Measurements'),
                Tables\Columns\IconColumn::make('needs_estimated_date')->boolean()->label('Est. Date'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('sort_order')->label('Sort')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([EditAction::make()])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrderTypes::route('/'),
            'create' => Pages\CreateOrderType::route('/create'),
            'edit'   => Pages\EditOrderType::route('/{record}/edit'),
        ];
    }
}
