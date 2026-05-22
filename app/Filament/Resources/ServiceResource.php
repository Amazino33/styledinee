<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string { return 'heroicon-o-scissors'; }
    public static function getNavigationGroup(): ?string { return 'Catalogue'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(Service::class, 'slug', ignoreRecord: true),

                Select::make('category')
                    ->required()
                    ->options([
                        'tailoring' => 'Bespoke Tailoring',
                        'dry_cleaning' => 'Dry Cleaning',
                        'alteration' => 'Alteration',
                        'delivery' => 'Pickup & Delivery',
                    ]),

                TextInput::make('base_price')
                    ->numeric()
                    ->prefix('₦')
                    ->nullable(),

                Textarea::make('short_description')
                    ->rows(2)
                    ->maxLength(300),

                RichEditor::make('description')
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->image()
                    ->directory('services')
                    ->columnSpanFull(),

                Toggle::make('is_active')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('base_price')->money('NGN')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options([
                    'tailoring' => 'Bespoke Tailoring',
                    'dry_cleaning' => 'Dry Cleaning',
                    'alteration' => 'Alteration',
                    'delivery' => 'Pickup & Delivery',
                ]),
            ])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
