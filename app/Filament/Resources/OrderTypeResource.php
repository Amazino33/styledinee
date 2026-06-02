<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderTypeResource\Pages;
use App\Models\OrderType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class OrderTypeResource extends Resource
{
    protected static ?string $model = OrderType::class;
    protected static null|BackedEnum|string $navigationIcon  = 'heroicon-o-tag';
    protected static null|UnitEnum|string   $navigationGroup = 'Settings';
    protected static ?string $navigationLabel  = 'Categories';
    protected static ?string $modelLabel       = 'Category';
    protected static ?string $pluralModelLabel = 'Categories';
    protected static ?int    $navigationSort   = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            // ── Identity ────────────────────────────────────────────────
            Section::make('Identity')->schema([
                TextInput::make('name')->required()->maxLength(100),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->helperText('Lowercase, underscores only. e.g. ready_made'),

                TextInput::make('icon')
                    ->maxLength(10)
                    ->helperText('Optional emoji shown in POS'),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),

                // Parent selector — any active category except the record itself and its descendants
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->placeholder('None (top-level category)')
                    ->options(function (?OrderType $record): array {
                        $exclude = $record ? $record->selfAndDescendantIds() : [];
                        return OrderType::flatTreeOptions($exclude);
                    })
                    ->nullable()
                    ->live()
                    ->searchable()
                    ->columnSpanFull(),
            ])->columns(2),

            // ── Behaviour ───────────────────────────────────────────────
            Section::make('Behaviour')
                ->description(fn (Get $get) => $get('parent_id')
                    ? 'Leave a field on "Inherit" to use the parent\'s value.'
                    : null)
                ->schema([
                    // Production path
                    Select::make('default_path_key')
                        ->label('Production Path')
                        ->options(function (Get $get): array {
                            $base = [
                                'none'                       => 'None (ready-made / collection)',
                                'sewing_only'                => 'Sewing → Finishing',
                                'sewing_embroidery'          => 'Sewing → Embroidery → Finishing',
                                'sewing_printing'            => 'Sewing → Printing → Finishing',
                                'sewing_embroidery_printing' => 'Sewing → Embroidery → Printing → Finishing',
                                'embroidery_only'            => 'Embroidery → Finishing',
                                'printing_only'              => 'Printing only',
                                'embroidery_printing'        => 'Embroidery → Printing → Finishing',
                            ];
                            if ($get('parent_id')) {
                                return ['' => '— Inherit from parent —'] + $base;
                            }
                            return $base;
                        })
                        ->default(fn (Get $get) => $get('parent_id') ? null : 'none')
                        ->nullable()
                        ->helperText('Default pipeline for items in this category.')
                        ->columnSpanFull(),

                    // Requires Production
                    Select::make('needs_production')
                        ->label('Requires Production')
                        ->options(fn (Get $get) => self::boolOptions($get('parent_id'), 'needs_production'))
                        ->default(fn (Get $get) => $get('parent_id') ? null : 1)
                        ->nullable()
                        ->helperText('Goes through the production/tailoring pipeline'),

                    // Requires Measurements
                    Select::make('needs_measurements')
                        ->label('Requires Measurements')
                        ->options(fn (Get $get) => self::boolOptions($get('parent_id'), 'needs_measurements'))
                        ->default(fn (Get $get) => $get('parent_id') ? null : 0)
                        ->nullable(),

                    // Show Estimated Date
                    Select::make('needs_estimated_date')
                        ->label('Show Estimated Completion Date')
                        ->options(fn (Get $get) => self::boolOptions($get('parent_id'), 'needs_estimated_date'))
                        ->default(fn (Get $get) => $get('parent_id') ? null : 1)
                        ->nullable(),

                    Toggle::make('is_active')->label('Active')->default(true),
                ])->columns(2),
        ]);
    }

    /** Builds Yes/No options, prepending an "Inherit" entry for subcategories. */
    private static function boolOptions(?int $parentId, string $field): array
    {
        $base = ['1' => 'Yes', '0' => 'No'];

        if (! $parentId) return $base;

        $parent = OrderType::with('parent.parent.parent')->find($parentId);
        $effective = match ($field) {
            'needs_production'     => $parent?->effective_needs_production,
            'needs_measurements'   => $parent?->effective_needs_measurements,
            'needs_estimated_date' => $parent?->effective_needs_estimated_date,
            default                => null,
        };

        $label = $effective === null
            ? '— Inherit from parent —'
            : '— Inherit from parent (' . ($effective ? 'Yes' : 'No') . ') —';

        return ['' => $label] + $base;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')->label('')->width(40),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function (string $state, OrderType $record): HtmlString {
                        $depth  = count($record->ancestorChain()) - 1;
                        $indent = $depth > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;', $depth) . '↳ ' : '';
                        return new HtmlString($indent . e($state));
                    }),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('slug')->badge()->color('gray'),

                Tables\Columns\IconColumn::make('effective_needs_production')
                    ->boolean()->label('Production')
                    ->getStateUsing(fn (OrderType $r) => $r->effective_needs_production),

                Tables\Columns\IconColumn::make('effective_needs_measurements')
                    ->boolean()->label('Measurements')
                    ->getStateUsing(fn (OrderType $r) => $r->effective_needs_measurements),

                Tables\Columns\IconColumn::make('effective_needs_estimated_date')
                    ->boolean()->label('Est. Date')
                    ->getStateUsing(fn (OrderType $r) => $r->effective_needs_estimated_date),

                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')->label('Sort')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrderTypes::route('/'),
            'create' => Pages\CreateOrderType::route('/create'),
            'edit'   => Pages\EditOrderType::route('/{record}/edit'),
        ];
    }
}
