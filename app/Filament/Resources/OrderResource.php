<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;
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

                    Select::make('delivery_type')
                        ->label('Delivery Type')
                        ->options(['pickup' => 'Pickup', 'delivery' => 'Home Delivery'])
                        ->disabled($isStaff)
                        ->live(),

                    Textarea::make('customer_address')
                        ->label('Delivery Address')
                        ->rows(2)
                        ->disabled($isStaff)
                        ->visible(fn (Get $get) => $get('delivery_type') === 'delivery')
                        ->columnSpanFull(),

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
                Tables\Columns\IconColumn::make('driver_cash_pending')
                    ->label('Cash Due')
                    ->boolean()
                    ->trueIcon('heroicon-o-banknotes')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-minus-small')
                    ->falseColor('gray')
                    ->tooltip(fn (bool $state) => $state ? 'Driver cash pending handover to cashier' : null),
                Tables\Columns\TextColumn::make('delivery_type')
                    ->label('Delivery')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match($state) {
                        'delivery' => '🚚 Home Delivery',
                        'pickup'   => '🏪 Pickup',
                        default    => '—',
                    })
                    ->color(fn (?string $state) => $state === 'delivery' ? 'info' : 'gray'),
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
                Tables\Filters\TernaryFilter::make('driver_cash_pending')
                    ->label('Driver Cash')
                    ->trueLabel('Pending Handover')
                    ->falseLabel('No Pending Handover')
                    ->placeholder('All orders'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),

                // ── Record Payment (transfer after delivery / debt balancing) ─────
                Action::make('recordPayment')
                    ->label('Record Payment')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) =>
                        $record->payment_status !== 'paid'
                        && ! $record->driver_cash_pending
                        && auth()->user()?->hasAnyRole(['admin', 'cashier'])
                    )
                    ->modalHeading(fn (Order $record) =>
                        'Record Payment — Balance Due: ₦' . number_format(
                            max(0, (float) $record->total_amount - (float) $record->amount_paid), 0
                        )
                    )
                    ->fillForm(fn (Order $record) => [
                        'amount' => number_format(
                            max(0, (float) $record->total_amount - (float) $record->amount_paid),
                            2, '.', ''
                        ),
                        'method' => 'transfer',
                    ])
                    ->form([
                        Select::make('method')
                            ->label('Payment Method')
                            ->required()
                            ->options([
                                'cash'     => '💵 Cash',
                                'transfer' => '🏦 Bank Transfer',
                                'card'     => '💳 Card',
                                'pos'      => '🖥 POS Terminal',
                            ])
                            ->live(),

                        TextInput::make('amount')
                            ->label('Amount (₦)')
                            ->required()
                            ->numeric()
                            ->minValue(0.01),

                        TextInput::make('reference')
                            ->label('Transfer Reference')
                            ->placeholder('Bank ref, last 4 digits, or transaction ID')
                            ->required(fn (Get $get) => $get('method') === 'transfer')
                            ->visible(fn (Get $get) => $get('method') === 'transfer'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->placeholder('Optional — e.g. "customer confirmed payment via call"'),
                    ])
                    ->action(function (Order $record, array $data) {
                        $balanceDue = max(0, (float) $record->total_amount - (float) $record->amount_paid);
                        $amount     = min((float) $data['amount'], $balanceDue);

                        if ($amount <= 0) {
                            Notification::make()->title('Invalid amount.')->danger()->send();
                            return;
                        }

                        $notes = trim($data['notes'] ?? '');
                        if ($data['method'] === 'transfer' && ! empty($data['reference'])) {
                            $notes = 'Transfer ref: ' . $data['reference'] . ($notes ? "\n{$notes}" : '');
                        }

                        $newAmountPaid = round((float) $record->amount_paid + $amount, 2);
                        $newStatus     = $newAmountPaid >= (float) $record->total_amount ? 'paid' : 'partial';

                        $record->update([
                            'amount_paid'    => $newAmountPaid,
                            'payment_status' => $newStatus,
                        ]);

                        $record->recordPayment($amount, $data['method'], $notes ?: null);

                        $others = User::role(['admin', 'cashier'])->where('id', '!=', auth()->id())->get();
                        if ($others->isNotEmpty()) {
                            FilamentNotification::make()
                                ->title('Payment recorded')
                                ->body('₦' . number_format($amount, 0) . ' via ' . Payment::methodLabel($data['method']) . ' for order ' . $record->reference . '.')
                                ->icon('heroicon-o-banknotes')
                                ->iconColor('success')
                                ->sendToDatabase($others);
                        }

                        $outstanding = (float) $record->total_amount - $newAmountPaid;
                        Notification::make()
                            ->title('Payment recorded — ' . ($newStatus === 'paid'
                                ? 'Order fully paid.'
                                : '₦' . number_format($outstanding, 0) . ' still outstanding.'))
                            ->success()
                            ->send();
                    }),

                // ── Confirm cash received from delivery driver ────────────────────
                Action::make('confirmDriverCash')
                    ->label('Confirm Cash from Driver')
                    ->icon('heroicon-o-hand-raised')
                    ->color('warning')
                    ->visible(fn (Order $record) =>
                        $record->driver_cash_pending
                        && auth()->user()?->hasAnyRole(['admin', 'cashier'])
                    )
                    ->requiresConfirmation()
                    ->modalHeading(fn (Order $record) => "Confirm Cash Receipt — {$record->reference}")
                    ->modalDescription(fn (Order $record) =>
                        'Confirm that you have physically received the cash for order ' .
                        $record->reference . ' (' . $record->customer_name . ') from the delivery driver. ' .
                        'This clears the pending handover flag.'
                    )
                    ->modalSubmitActionLabel('Yes, I Have the Cash')
                    ->action(function (Order $record) {
                        $record->update(['driver_cash_pending' => false]);

                        $others = User::role(['admin', 'cashier'])->where('id', '!=', auth()->id())->get();
                        if ($others->isNotEmpty()) {
                            FilamentNotification::make()
                                ->title('Driver cash confirmed')
                                ->body('Cash for order ' . $record->reference . ' confirmed received by ' . auth()->user()->name . '.')
                                ->icon('heroicon-o-hand-raised')
                                ->iconColor('success')
                                ->sendToDatabase($others);
                        }

                        Notification::make()->title('Cash receipt confirmed.')->success()->send();
                    }),

                Action::make('changeDelivery')
                    ->label('Change Delivery')
                    ->icon('heroicon-o-truck')
                    ->color('gray')
                    ->visible(fn (Order $record) =>
                        ! in_array($record->status, ['delivered', 'cancelled'])
                        && auth()->user()?->hasAnyRole(['admin', 'cashier'])
                    )
                    ->fillForm(fn (Order $record) => [
                        'delivery_type'    => $record->delivery_type,
                        'customer_address' => $record->customer_address,
                        'delivery_notes'   => $record->delivery_notes,
                    ])
                    ->form([
                        Select::make('delivery_type')
                            ->label('Delivery Type')
                            ->options(['pickup' => '🏪 Pickup', 'delivery' => '🚚 Home Delivery'])
                            ->required()
                            ->live(),

                        Textarea::make('customer_address')
                            ->label('Delivery Address')
                            ->rows(2)
                            ->required(fn (Get $get) => $get('delivery_type') === 'delivery')
                            ->visible(fn (Get $get) => $get('delivery_type') === 'delivery')
                            ->helperText('Required for home delivery.'),

                        Textarea::make('delivery_notes')
                            ->label('Delivery Notes')
                            ->rows(2)
                            ->placeholder('e.g. Call on arrival, gate code…')
                            ->visible(fn (Get $get) => $get('delivery_type') === 'delivery'),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update([
                            'delivery_type'    => $data['delivery_type'],
                            'customer_address' => $data['customer_address'] ?? $record->customer_address,
                            'delivery_notes'   => $data['delivery_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title('Delivery type updated to ' . ($data['delivery_type'] === 'delivery' ? 'Home Delivery' : 'Pickup'))
                            ->success()
                            ->send();
                    }),
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

