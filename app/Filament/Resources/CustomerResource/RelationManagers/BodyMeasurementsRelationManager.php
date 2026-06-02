<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\MeasurementField;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid as ComponentsGrid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BodyMeasurementsRelationManager extends RelationManager
{
    protected static string $relationship = 'bodyMeasurements';
    protected static ?string $title = 'Body Measurements';
    protected static bool $shouldSkipAuthorization = true;

    public function form(Schema $schema): Schema
    {
        $fields = MeasurementField::where('is_active', true)->orderBy('label')->get();

        $measurementInputs = $fields->map(fn ($field) =>
            TextInput::make("measurements.{$field->name}")
                ->label($field->label)
                ->numeric()
                ->minValue(0)
                ->step(0.5)
                ->suffix(fn ($get) => $get('unit') === 'cm' ? 'cm' : 'in')
        )->all();

        return $schema->components([
            Select::make('unit')
                ->options(['inches' => 'Inches', 'cm' => 'Centimetres'])
                ->default('inches')
                ->required()
                ->live()
                ->columnSpanFull(),

            ComponentsGrid::make(3)
                ->schema($measurementInputs)
                ->columnSpanFull(),

            DateTimePicker::make('taken_at')
                ->label('Date Taken')
                ->default(now())
                ->nullable(),

            Textarea::make('notes')
                ->rows(2)
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('taken_at')
                    ->label('Date Taken')
                    ->date()
                    ->sortable()
                    ->placeholder('Not recorded'),

                Tables\Columns\TextColumn::make('unit')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('measurements')
                    ->label('Fields')
                    ->getStateUsing(fn ($record) => count(array_filter($record->measurements ?? [], fn ($v) => $v !== null && $v !== '')) . ' filled')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('is_active', 'desc')
            ->recordActions([
                Action::make('set_active')
                    ->label('Set as Active')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->visible(fn ($record) => ! $record->is_active)
                    ->action(function ($record) {
                        $record->setAsActive();
                        Notification::make()
                            ->title('Active profile updated.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Record Measurements')
                    ->after(function ($record) {
                        // Auto-activate if this is the first profile
                        $count = $record->customer->bodyMeasurements()->count();
                        if ($count === 1) {
                            $record->setAsActive();
                        }
                    }),
            ]);
    }
}
