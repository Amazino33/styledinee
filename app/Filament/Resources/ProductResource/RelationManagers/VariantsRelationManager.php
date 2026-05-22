<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static bool $shouldSkipAuthorization = true;

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('variant_type')
                ->label('Variant Type')
                ->options([
                    'size'  => 'Size',
                    'class' => 'Class',
                    'color' => 'Color',
                    'style' => 'Style',
                ])
                ->required(),

            TextInput::make('variant_value')
                ->label('Variant Value')
                ->placeholder('e.g. XL, Class 2, Gold')
                ->required(),

            TextInput::make('price_adjustment')
                ->label('Price Adjustment (₦)')
                ->numeric()
                ->default(0)
                ->prefix('₦')
                ->helperText('Added to base product price. Use 0 for no adjustment.'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('variant_type')->badge()->sortable(),
                Tables\Columns\TextColumn::make('variant_value')->label('Value'),
                Tables\Columns\TextColumn::make('price_adjustment')
                    ->label('±Price')
                    ->money('NGN')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('variant_type')
            ->headerActions([\Filament\Actions\CreateAction::make()])
            ->actions([\Filament\Actions\EditAction::make(), \Filament\Actions\DeleteAction::make()])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
