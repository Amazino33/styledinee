<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\MeasurementField;
use App\Models\OrderType;
use App\Models\Product;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
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

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            // ── 1. Basic Info + Publishing ───────────────────────────────
            Grid::make(2)->schema([
                Section::make('Basic Info')->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),

                    TextInput::make('slug')
                        ->required()
                        ->unique(Product::class, 'slug', ignoreRecord: true),

                    // ── Category cascade (levels 1–4, dehydrated:false) ────────
                    // order_type_id, production_type, product_type are Hidden fields
                    // set automatically by afterStateUpdated on each level.
                    Select::make('_cat_l1')
                        ->label('Category')
                        ->options(fn () => OrderType::whereNull('parent_id')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->required()
                        ->searchable()
                        ->live()
                        ->dehydrated(false)
                        ->afterStateUpdated(function ($state, Set $set) {
                            $set('_cat_l2', null);
                            $set('_cat_l3', null);
                            $set('_cat_l4', null);
                            self::syncCategoryFields($set, $state ? (int) $state : null);
                        })
                        ->columnSpanFull(),

                    Select::make('_cat_l2')
                        ->label('Subcategory')
                        ->options(fn (Get $get) => $get('_cat_l1')
                            ? OrderType::where('parent_id', (int) $get('_cat_l1'))
                                ->where('is_active', true)->orderBy('sort_order')
                                ->pluck('name', 'id')->toArray()
                            : [])
                        ->visible(fn (Get $get) => $get('_cat_l1') &&
                            OrderType::where('parent_id', (int) $get('_cat_l1'))->exists())
                        ->nullable()->searchable()->live()->dehydrated(false)
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $set('_cat_l3', null);
                            $set('_cat_l4', null);
                            $id = $state ? (int) $state : ($get('_cat_l1') ? (int) $get('_cat_l1') : null);
                            self::syncCategoryFields($set, $id);
                        })
                        ->columnSpanFull(),

                    Select::make('_cat_l3')
                        ->label('Sub-subcategory')
                        ->options(fn (Get $get) => $get('_cat_l2')
                            ? OrderType::where('parent_id', (int) $get('_cat_l2'))
                                ->where('is_active', true)->orderBy('sort_order')
                                ->pluck('name', 'id')->toArray()
                            : [])
                        ->visible(fn (Get $get) => $get('_cat_l2') &&
                            OrderType::where('parent_id', (int) $get('_cat_l2'))->exists())
                        ->nullable()->searchable()->live()->dehydrated(false)
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $set('_cat_l4', null);
                            $id = $state ? (int) $state : ($get('_cat_l2') ? (int) $get('_cat_l2') : null);
                            self::syncCategoryFields($set, $id);
                        })
                        ->columnSpanFull(),

                    Select::make('_cat_l4')
                        ->label('Sub-sub-subcategory')
                        ->options(fn (Get $get) => $get('_cat_l3')
                            ? OrderType::where('parent_id', (int) $get('_cat_l3'))
                                ->where('is_active', true)->orderBy('sort_order')
                                ->pluck('name', 'id')->toArray()
                            : [])
                        ->visible(fn (Get $get) => $get('_cat_l3') &&
                            OrderType::where('parent_id', (int) $get('_cat_l3'))->exists())
                        ->nullable()->searchable()->live()->dehydrated(false)
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $id = $state ? (int) $state : ($get('_cat_l3') ? (int) $get('_cat_l3') : null);
                            self::syncCategoryFields($set, $id);
                        })
                        ->columnSpanFull(),

                    Hidden::make('order_type_id'),
                    Hidden::make('production_type')->default('ready_made'),
                    Hidden::make('product_type')->default(''),
                    Hidden::make('needs_measurements')->default('0'),

                    TextInput::make('estimated_production_hours')
                        ->label('Est. Production Hours')
                        ->numeric()
                        ->minValue(1)
                        ->visible(fn (Get $get) => $get('production_type') === 'production'),

                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2)->columnSpan(1),

                Section::make('Publishing')->schema([
                    Toggle::make('is_active')
                        ->label('Active (available in POS)')
                        ->default(true),

                    Toggle::make('is_published')
                        ->label('Published (visible on website)')
                        ->helperText('Only published products appear on the public shop page.')
                        ->default(false),

                    Toggle::make('is_embroidery')
                        ->label('Has Embroidery')
                        ->helperText('When set, orders containing this item will include the embroidery stage.')
                        ->default(false)
                        ->visible(fn (Get $get) => $get('production_type') === 'production'),

                    Toggle::make('is_material')
                        ->label('Use as Material')
                        ->helperText('When enabled, this product appears as an option in other products\' Bill of Materials.')
                        ->default(false)
                        ->live(),

                    TextInput::make('unit')
                        ->label('Unit of Measure')
                        ->placeholder('e.g. metres, pieces, litres')
                        ->visible(fn ($get) => (bool) $get('is_material')),

                    TextInput::make('sort_order')->numeric()->default(0),
                ])->columnSpan(1),
            ])->columnSpanFull(),

            // ── 2. Bill of Materials ─────────────────────────────────────
            Section::make('Bill of Materials (BOM)')
                ->description('Materials required to make this product.')
                ->schema([
                    Repeater::make('materials')
                        ->relationship()
                        ->schema([
                            Select::make('material_id')
                                ->label('Material Product')
                                ->options(
                                    Product::where('is_material', true)
                                        ->where('is_active', true)
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn ($p) => [$p->id => $p->name . ($p->unit ? " ({$p->unit})" : '')])
                                )
                                ->required()
                                ->searchable()
                                ->columnSpan(3)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $material = Product::find($state);
                                    $set('unit_price', $material ? (float) $material->price : 0);
                                    $qty = (float) ($get('quantity') ?: 1);
                                    $set('line_total', $material ? round((float) $material->price * $qty, 2) : 0);
                                }),

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required()
                                ->minValue(0.001)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    $price = (float) ($get('unit_price') ?: 0);
                                    $set('line_total', round($price * (float) ($state ?: 0), 2));
                                }),

                            TextInput::make('unit_price')
                                ->label('Unit Price (₦)')
                                ->numeric()
                                ->prefix('₦')
                                ->disabled()
                                ->dehydrated(false),

                            TextInput::make('line_total')
                                ->label('Line Total (₦)')
                                ->numeric()
                                ->prefix('₦')
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(6)
                        ->defaultItems(0)
                        ->addActionLabel('Add Material')
                        ->itemLabel(fn (array $state): ?string =>
                            ($state['material_id']
                                ? (Product::find($state['material_id'])?->name ?? '')
                                : null)
                        ),

                    Placeholder::make('bom_total')
                        ->label('Total Material Cost')
                        ->content(function (Get $get): \Illuminate\Support\HtmlString {
                            $materials = $get('materials') ?? [];
                            $total = 0.0;
                            foreach ($materials as $line) {
                                $materialId = $line['material_id'] ?? null;
                                $qty        = (float) ($line['quantity'] ?? 0);
                                if ($materialId && $qty > 0) {
                                    $material = Product::find($materialId);
                                    if ($material) {
                                        $total += (float) $material->price * $qty;
                                    }
                                }
                            }
                            return new \Illuminate\Support\HtmlString(
                                '<p class="text-sm text-gray-500 dark:text-gray-400">Estimated cost to produce one unit based on selected materials.</p>'
                                . '<p class="text-2xl font-bold text-primary-600 mt-1">₦' . number_format(round($total, 2), 2) . '</p>'
                            );
                        })
                        ->columnSpanFull(),
                ])
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('production_type') === 'production'),

            // ── 3. Measurements ──────────────────────────────────────────
            Section::make('Measurement Template')
                ->description('Fields required when a customer orders this product.')
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
                ->columnSpanFull()
                ->visible(fn (Get $get) => $get('needs_measurements') === '1'),

            Hidden::make('_bom_warning_acknowledged')->default(false),

            // ── 4. Pricing & Media ───────────────────────────────────────
            Section::make('Pricing & Media')->schema([
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₦')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set) => $set('_bom_warning_acknowledged', false)),
                
                TextInput::make('cost_price')
                    ->required()
                    ->numeric()
                    ->prefix('₦')
                    ->label('Cost Price'),

                TextInput::make('stock_quantity')
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    /**
     * Sets order_type_id, production_type, and product_type based on the deepest
     * selected category in the cascade chain.
     */
    private static function syncCategoryFields(Set $set, ?int $categoryId): void
    {
        $set('order_type_id', $categoryId);
        if (! $categoryId) {
            $set('production_type', 'ready_made');
            $set('product_type', '');
            $set('needs_measurements', '0');
            return;
        }
        $cat = OrderType::with('parent.parent.parent')->find($categoryId);
        if (! $cat) return;
        $set('production_type',   $cat->effective_needs_production   ? 'production' : 'ready_made');
        $set('product_type',      $cat->slug);
        $set('needs_measurements', $cat->effective_needs_measurements ? '1' : '0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('product_type')->label('Category')->badge()
                    ->color(fn ($state) => in_array($state, ['ready_made', 'accessory']) ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('price')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Published'),
                Tables\Columns\IconColumn::make('is_embroidery')->boolean()->label('Embroidery'),
                Tables\Columns\TextColumn::make('cost_price')
                    ->money('NGN')
                    ->label('Cost Price')
                    ->sortable(),

                Tables\Columns\TextColumn::make('profit')
                    ->label('Profit')
                    ->money('NGN')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->price - $record->cost_price)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('order_type_id')
                    ->label('Category')
                    ->relationship('orderType', 'name'),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published')
                    ->trueLabel('Published only')
                    ->falseLabel('Unpublished only')
                    ->placeholder('All products'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->placeholder('All'),
            ])
            ->recordActions([\Filament\Actions\EditAction::make()])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('publish')
                        ->label('Publish selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['is_published' => true]))
                        ->deselectRecordsAfterCompletion(),
                    \Filament\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('gray')
                        ->action(fn ($records) => $records->each->update(['is_published' => false]))
                        ->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
