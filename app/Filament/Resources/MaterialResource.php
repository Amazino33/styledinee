<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationLabel = 'Materials';
    protected static ?int $navigationSort = 3;

    public static function getNavigationIcon(): string { return 'heroicon-o-beaker'; }
    public static function getNavigationGroup(): ?string { return 'Catalogue'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Material Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('unit')
                    ->label('Unit of Measure')
                    ->placeholder('metres, pieces, buttons, yards…')
                    ->required()
                    ->default('piece'),

                TextInput::make('description')
                    ->label('Description')
                    ->maxLength(500)
                    ->columnSpanFull(),

                Toggle::make('is_active')->default(true),
            ])->columns(2),

            Section::make('Stock Tracking')
                ->description('Track how much of this material is in stock.')
                ->schema([
                    TextInput::make('stock_quantity')
                        ->label('Current Stock')
                        ->numeric()
                        ->default(0)
                        ->suffix(fn ($get) => $get('unit') ?: 'units'),

                    TextInput::make('low_stock_threshold')
                        ->label('Low Stock Alert Below')
                        ->numeric()
                        ->default(5)
                        ->suffix(fn ($get) => $get('unit') ?: 'units'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit')->badge(),
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('In Stock')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit)
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('low_stock_threshold')
                    ->label('Alert Below')
                    ->formatStateUsing(fn ($state, $record) => $state . ' ' . $record->unit),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit'   => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
