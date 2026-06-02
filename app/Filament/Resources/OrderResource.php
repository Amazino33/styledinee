<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string { return 'heroicon-o-clipboard-document-list'; }
    public static function getNavigationGroup(): ?string { return 'Orders'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'cashier']);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if ($user->hasRole('admin')) return true;
        if ($user->hasRole('cashier')) return true;
        if ($user->hasRole('tailor') && in_array($record->type, ['tailoring', 'alteration'])) return true;
        if ($user->hasRole('dry_cleaner') && $record->type === 'dry_cleaning') return true;
        if ($user->hasRole('delivery') && $record->type === 'pickup_delivery') return true;
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        $user = auth()->user();
        $isStaff = $user?->hasAnyRole(['tailor', 'dry_cleaner', 'delivery']);

        return $schema->components([
            Section::make('Customer Details')
                ->schema([
                    TextInput::make('customer_name')->required()->disabled($isStaff),
                    TextInput::make('customer_email')->email()->required()->disabled($isStaff),
                    TextInput::make('customer_phone')->required()->disabled($isStaff),
                    Textarea::make('customer_address')->rows(2)->disabled($isStaff),
                ])->columns(2),

            Section::make('Order Details')
                ->schema([
                    TextInput::make('reference')->disabled(),

                    Select::make('type')
                        ->required()
                        ->disabled($isStaff)
                        ->options([
                            'tailoring' => 'Bespoke Tailoring',
                            'dry_cleaning' => 'Dry Cleaning',
                            'alteration' => 'Alteration',
                            'pickup_delivery' => 'Pickup & Delivery',
                        ]),

                    Select::make('status')
                        ->required()
                        ->options([
                            'pending'     => 'Pending',
                            'confirmed'   => 'Confirmed',
                            'in_progress' => 'In Progress',
                            'ready'       => 'Ready',
                            'dispatched'  => 'Dispatched',
                            'delivered'   => 'Delivered',
                            'cancelled'   => 'Cancelled',
                        ]),

                    Select::make('payment_status')
                        ->disabled(! $user?->hasAnyRole(['admin', 'cashier']))
                        ->options([
                            'unpaid' => 'Unpaid',
                            'partial' => 'Partial',
                            'paid' => 'Paid',
                        ]),

                    TextInput::make('total_amount')->numeric()->prefix('â‚¦')->disabled($isStaff),
                    TextInput::make('amount_paid')->numeric()->prefix('â‚¦')
                        ->disabled(! $user?->hasAnyRole(['admin', 'cashier'])),

                    DatePicker::make('pickup_date')->disabled($isStaff),
                    DatePicker::make('delivery_date'),
                ])->columns(2),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),

            Section::make('Order Items')
                ->schema([
                    Repeater::make('items')
                        ->relationship()
                        ->schema([
                            TextInput::make('description')->required()->columnSpan(3),
                            TextInput::make('quantity')->numeric()->default(1)->columnSpan(1),
                            TextInput::make('unit_price')->numeric()->prefix('â‚¦')->columnSpan(2)
                                ->live()
                                ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                    $set('subtotal', (float) $state * (int) $get('quantity'));
                                }),
                            TextInput::make('subtotal')->numeric()->prefix('â‚¦')->disabled()->columnSpan(2),
                        ])
                        ->columns(8)
                        ->disabled($isStaff),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('customer_name')->searchable(),
                Tables\Columns\TextColumn::make('customer_phone'),
                Tables\Columns\TextColumn::make('type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tailoring' => 'primary',
                        'dry_cleaning' => 'success',
                        'alteration' => 'warning',
                        'pickup_delivery' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'gray',
                        'confirmed'   => 'info',
                        'in_progress' => 'warning',
                        'ready'       => 'success',
                        'dispatched'  => 'primary',
                        'delivered'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'partial' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')->money('NGN')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'tailoring' => 'Tailoring',
                    'dry_cleaning' => 'Dry Cleaning',
                    'alteration' => 'Alteration',
                    'pickup_delivery' => 'Pickup & Delivery',
                ]),
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending'     => 'Pending',
                    'confirmed'   => 'Confirmed',
                    'in_progress' => 'In Progress',
                    'ready'       => 'Ready',
                    'dispatched'  => 'Dispatched',
                    'delivered'   => 'Delivered',
                    'cancelled'   => 'Cancelled',
                ]),
                Tables\Filters\SelectFilter::make('payment_status')->options([
                    'unpaid' => 'Unpaid',
                    'partial' => 'Partial',
                    'paid' => 'Paid',
                ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

