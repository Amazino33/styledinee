<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\Customer;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationLabel = 'Coupons';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string { return 'heroicon-o-ticket'; }
    public static function getNavigationGroup(): ?string { return 'Referral & Rewards'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Coupon Details')
                ->schema([
                    TextInput::make('code')
                        ->required()->maxLength(50)
                        ->unique(Coupon::class, 'code', ignoreRecord: true)
                        ->hint('Customers and cashiers enter this code at checkout.')
                        ->formatStateUsing(fn ($state) => strtoupper($state ?? ''))
                        ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                    TextInput::make('name')->required()->maxLength(255)
                        ->hint('E.g. "Return Customer — May Promo"'),
                    Textarea::make('description')->rows(2)->columnSpanFull(),
                ])->columns(2),

            Section::make('Discount')
                ->schema([
                    Select::make('type')
                        ->options(['fixed' => 'Fixed Amount (₦)', 'percentage' => 'Percentage (%)'])
                        ->required()->default('fixed')->live(),
                    TextInput::make('amount')
                        ->required()->numeric()->minValue(0.01)
                        ->label(fn ($get) => $get('type') === 'percentage' ? 'Percentage (%)' : 'Amount (₦)')
                        ->suffix(fn ($get) => $get('type') === 'percentage' ? '%' : null)
                        ->prefix(fn ($get) => $get('type') === 'fixed' ? '₦' : null),
                    TextInput::make('max_discount_amount')
                        ->label('Max Discount Cap (₦)')->prefix('₦')->numeric()->nullable()
                        ->visible(fn ($get) => $get('type') === 'percentage')
                        ->hint('Caps the maximum discount for % coupons.'),
                    TextInput::make('min_order_amount')
                        ->label('Minimum Order (₦)')->prefix('₦')->numeric()->nullable(),
                ])->columns(2),

            Section::make('Eligibility')
                ->schema([
                    Select::make('eligibility_rule')
                        ->label('Who can use this?')
                        ->options([
                            'public'              => 'Public — anyone',
                            'first_order'         => 'First Order — new customers only',
                            'return_customer'     => 'Return Customer — has placed at least 1 order',
                            'long_time_purchaser' => 'Long-time Purchaser — first order was X months ago',
                            'exclusive'           => 'Exclusive — specific customers only',
                        ])
                        ->default('public')->required()->live(),
                    TextInput::make('eligibility_months')
                        ->label('Months Since First Order')
                        ->numeric()->minValue(1)->nullable()
                        ->visible(fn ($get) => $get('eligibility_rule') === 'long_time_purchaser')
                        ->hint('e.g. 3 = customer whose first order was at least 3 months ago'),
                    Select::make('exclusiveCustomers')
                        ->label('Assign to Customers')
                        ->relationship('exclusiveCustomers', 'name')
                        ->multiple()->searchable()->preload()
                        ->visible(fn ($get) => $get('eligibility_rule') === 'exclusive')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Usage Limits & Validity')
                ->schema([
                    TextInput::make('usage_limit')
                        ->label('Total Redemptions Allowed')->numeric()->nullable()
                        ->hint('Leave blank for unlimited.'),
                    TextInput::make('usage_limit_per_customer')
                        ->label('Per-Customer Limit')->numeric()->nullable()
                        ->hint('Leave blank for unlimited per person.'),
                    DateTimePicker::make('starts_at')->label('Active From')->nullable(),
                    DateTimePicker::make('expires_at')->label('Expires At')->nullable(),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()->sortable()->badge()->color('primary'),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state === 'fixed'
                            ? '₦' . number_format($record->amount, 0)
                            : $record->amount . '%'
                    )->badge()->color('gray'),
                Tables\Columns\TextColumn::make('eligibility_rule')
                    ->label('Eligibility')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'public'              => 'Public',
                        'first_order'         => 'First Order',
                        'return_customer'     => 'Return Customer',
                        'long_time_purchaser' => 'Long-time Purchaser',
                        'exclusive'           => 'Exclusive',
                        default               => ucfirst($state),
                    })->badge()->color('info'),
                Tables\Columns\TextColumn::make('used_count')->label('Used')->sortable(),
                Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->formatStateUsing(fn ($state) => $state ?? '∞'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('expires_at')->label('Expires')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('eligibility_rule')
                    ->label('Eligibility')
                    ->options([
                        'public'              => 'Public',
                        'first_order'         => 'First Order',
                        'return_customer'     => 'Return Customer',
                        'long_time_purchaser' => 'Long-time Purchaser',
                        'exclusive'           => 'Exclusive',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status')
                    ->trueLabel('Active')->falseLabel('Inactive'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit'   => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
