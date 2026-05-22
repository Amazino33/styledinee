<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Material;
use App\Models\MeasurementField;
use App\Models\Product;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string { return 'heroicon-o-shopping-bag'; }
    public static function getNavigationGroup(): ?string { return 'Catalogue'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Product Details')->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->unique(Product::class, 'slug', ignoreRecord: true),

                Select::make('category')
                    ->options([
                        'fabric'     => 'Fabric',
                        'accessory'  => 'Accessory',
                        'ready_made' => 'Ready-Made',
                        'garment'    => 'Garment',
                    ]),

                Select::make('product_type')
                    ->label('Product Type')
                    ->options([
                        'ready_made' => 'Ready-Made',
                        'embroidery' => 'Embroidery',
                        'printing'   => 'Printing',
                        'fabric'     => 'Fabric',
                        'accessory'  => 'Accessory',
                    ])
                    ->default('ready_made')
                    ->required()
                    ->live(),

                Select::make('production_type')
                    ->label('Requires Production')
                    ->options([
                        'ready_made' => 'No (ready-made)',
                        'production' => 'Yes (tailoring/production)',
                    ])
                    ->default('ready_made')
                    ->required()
                    ->live(),

                TextInput::make('estimated_production_hours')
                    ->label('Est. Production Hours')
                    ->numeric()
                    ->minValue(1)
                    ->visible(fn ($get) => $get('production_type') === 'production'),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₦'),

                TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull(),

                Toggle::make('is_active')->default(true),
                Toggle::make('is_embroidery')
                    ->label('Is Embroidery Product')
                    ->helperText('When set, orders containing this item will include the embroidery stage.')
                    ->default(false)
                    ->visible(fn ($get) => $get('production_type') === 'production'),
                TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),

            Section::make('Measurement Template')
                ->description('Select the measurement fields required when a customer orders this product.')
                ->relationship('measurementTemplate')
                ->schema([
                    CheckboxList::make('fields')
                        ->label('Required Measurements')
                        ->options(
                            MeasurementField::where('is_active', true)
                                ->orderBy('label')
                                ->pluck('label', 'id')
                                ->toArray()
                        )
                        ->columns(3)
                        ->searchable(),
                ])
                ->visible(fn ($get) => $get('production_type') === 'production')
                ->collapsed(),

            Section::make('Bill of Materials (BOM)')
                ->description('Select materials required to make this product and specify quantities.')
                ->schema([
                    Repeater::make('materials')
                        ->relationship()
                        ->schema([
                            Select::make('material_id')
                                ->label('Material')
                                ->options(
                                    Material::where('is_active', true)
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn ($m) => [$m->id => "{$m->name} ({$m->unit})"])
                                )
                                ->required()
                                ->searchable(),
                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(0.001),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Add Material'),
                ])
                ->visible(fn ($get) => $get('production_type') === 'production')
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('production_type')->badge()
                    ->color(fn ($state) => $state === 'production' ? 'warning' : 'success'),
                Tables\Columns\TextColumn::make('price')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_embroidery')->boolean()->label('Embroidery'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')->options([
                    'fabric'     => 'Fabric',
                    'accessory'  => 'Accessory',
                    'ready_made' => 'Ready-Made',
                    'garment'    => 'Garment',
                ]),
                Tables\Filters\SelectFilter::make('production_type')->options([
                    'ready_made' => 'Ready-Made',
                    'production' => 'Production',
                ]),
            ])
            ->actions([\Filament\Actions\EditAction::make()])
            ->bulkActions([\Filament\Actions\BulkActionGroup::make([\Filament\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ProductResource\RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
