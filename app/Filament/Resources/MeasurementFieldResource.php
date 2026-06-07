<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeasurementFieldResource\Pages;
use App\Models\MeasurementField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class MeasurementFieldResource extends Resource
{
    protected static ?string $model = MeasurementField::class;
    protected static ?string $navigationLabel = 'Measurement Fields';
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string { return 'heroicon-o-variable'; }
    public static function getNavigationGroup(): ?string { return 'Catalogue'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('label')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if (! $get('is_system')) {
                            $set('name', Str::snake(strtolower($state)));
                        }
                    }),

                TextInput::make('name')
                    ->required()
                    ->unique(MeasurementField::class, 'name', ignoreRecord: true)
                    ->helperText('Snake_case identifier, auto-generated from label.')
                    ->disabled(fn ($record) => $record?->is_system),

                Toggle::make('is_active')->default(true)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->copyable()->fontFamily('mono'),
                Tables\Columns\IconColumn::make('is_system')->boolean()->label('System'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('label')
            ->filters([])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $records->filter(fn ($r) => $r->is_system)->each(function ($r) {
                                \Filament\Notifications\Notification::make()
                                    ->title("Cannot delete system field: {$r->label}")
                                    ->warning()->send();
                            });
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMeasurementFields::route('/'),
            'create' => Pages\CreateMeasurementField::route('/create'),
            'edit'   => Pages\EditMeasurementField::route('/{record}/edit'),
        ];
    }
}
