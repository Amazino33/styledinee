<?php
namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?int $navigationSort = 0;

    public static function getNavigationIcon(): string { return 'heroicon-o-users'; }
    public static function getNavigationGroup(): ?string { return 'Customers'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('phone')->required()->tel(),
                TextInput::make('email')->email()->nullable(),
                Textarea::make('address')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable()->toggleable(),
            ])
            ->filters([])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\CustomerResource\RelationManagers\BodyMeasurementsRelationManager::class,
            \App\Filament\Resources\CustomerResource\RelationManagers\MeasurementsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit'   => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}

