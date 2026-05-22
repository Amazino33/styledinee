<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\MeasurementField;
use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MeasurementsRelationManager extends RelationManager
{
    protected static string $relationship = 'measurements';
    protected static ?string $title = 'Measurements';
    protected static bool $shouldSkipAuthorization = true;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')
                ->label('Product')
                ->options(
                    Product::where('is_active', true)
                        ->whereHas('measurementTemplate')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->required()
                ->live()
                ->searchable()
                ->columnSpanFull(),

            Placeholder::make('measurements_hint')
                ->label('')
                ->content('Select a product above to load its measurement fields.')
                ->visible(fn (Get $get) => ! $get('product_id'))
                ->columnSpanFull(),

            Grid::make(3)
                ->schema(function (Get $get) {
                    $productId = $get('product_id');
                    if (! $productId) return [];

                    $product = Product::with('measurementTemplate')->find($productId);
                    if (! $product?->measurementTemplate || empty($product->measurementTemplate->fields)) return [];

                    return MeasurementField::whereIn('id', $product->measurementTemplate->fields)
                        ->orderBy('label')
                        ->get()
                        ->map(fn (MeasurementField $field) =>
                            TextInput::make("measurements.{$field->id}")
                                ->label($field->label)
                                ->numeric()
                                ->minValue(0)
                                ->step(0.5)
                                ->suffix('in')
                        )
                        ->toArray();
                })
                ->visible(fn (Get $get) => (bool) $get('product_id'))
                ->columnSpanFull(),

            Select::make('unit')
                ->options(['inches' => 'Inches', 'cm' => 'Centimetres'])
                ->default('inches')
                ->required(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')->badge(),
                Tables\Columns\TextColumn::make('measurements')
                    ->label('Fields Recorded')
                    ->getStateUsing(fn ($record) => count(array_filter($record->measurements ?? [])) . ' fields'),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->headerActions([\Filament\Actions\CreateAction::make()])
            ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
