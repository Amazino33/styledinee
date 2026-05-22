<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderStatusLogResource\Pages;
use App\Models\OrderStatusLog;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OrderStatusLogResource extends Resource
{
    protected static ?string $model = OrderStatusLog::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Status Logs';

    public static function getNavigationIcon(): string { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): ?string { return 'Operations'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order.reference')->disabled(),
            TextInput::make('status')->disabled(),
            TextInput::make('changedBy.name')->label('Changed By')->disabled(),
            Textarea::make('notes')->disabled()->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('changedBy.name')->label('Changed By'),
                Tables\Columns\TextColumn::make('notes')->limit(60),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderStatusLogs::route('/'),
        ];
    }
}
