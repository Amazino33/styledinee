<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateResource\Pages;
use App\Models\Affiliate;
use App\Models\AppSetting;
use App\Models\Customer;
use App\Models\ReferralCreditLedger;
use App\Services\ReferralService;
use Filament\Actions\Action;
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

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;
    protected static ?string $navigationLabel = 'Affiliates';
    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): string { return 'heroicon-o-user-plus'; }
    public static function getNavigationGroup(): ?string { return 'Referral & Rewards'; }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Identity')
                ->schema([
                    TextInput::make('username')
                        ->required()->maxLength(50)->unique(Affiliate::class, 'username', ignoreRecord: true)
                        ->hint('This is their referral code.'),
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->nullable(),
                    TextInput::make('phone')->tel()->nullable(),
                ])->columns(2),

            Section::make('Link to Existing Account')
                ->description('If this affiliate is also a registered customer or staff member, link them here.')
                ->schema([
                    Select::make('customer_id')
                        ->label('Customer Account')
                        ->relationship('customer', 'name')
                        ->searchable()->nullable(),
                    Select::make('user_id')
                        ->label('Staff Account')
                        ->relationship('user', 'name')
                        ->searchable()->nullable(),
                ])->columns(2)->collapsed(),

            Section::make('Commission & Payout')
                ->schema([
                    TextInput::make('commission_rate')
                        ->label('Commission Rate (%)')
                        ->numeric()->minValue(0)->maxValue(100)->nullable()
                        ->hint('Leave blank to use the global default rate.'),
                    Select::make('referral_payout_type')
                        ->label('Referral Reward Payout')
                        ->options(['credit' => 'Account Credit', 'bank_transfer' => 'Bank Transfer'])
                        ->default('credit')->required(),
                    Select::make('affiliate_payout_type')
                        ->label('Commission Payout')
                        ->options(['credit' => 'Account Credit', 'bank_transfer' => 'Bank Transfer'])
                        ->default('bank_transfer')->required(),
                ])->columns(3),

            Section::make('Bank Details')
                ->schema([
                    TextInput::make('bank_name')->maxLength(100),
                    TextInput::make('account_number')->maxLength(20),
                    TextInput::make('account_name')->maxLength(150),
                ])->columns(3)->collapsed(),

            Section::make('Status & Notes')
                ->schema([
                    Select::make('status')
                        ->options(['pending' => 'Pending', 'active' => 'Active', 'suspended' => 'Suspended'])
                        ->default('pending')->required(),
                    DateTimePicker::make('approved_at')->label('Approved At')->nullable(),
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Code')->searchable()->sortable()
                    ->badge()->color('primary'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->label('Rate %')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : 'Default')
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'    => 'success',
                        'pending'   => 'warning',
                        'suspended' => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('commissions_count')
                    ->counts('commissions')->label('Commissions')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'active' => 'Active', 'suspended' => 'Suspended']),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Affiliate $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Affiliate $record) {
                        $record->update([
                            'status'      => 'active',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
                Action::make('approve_commission')
                    ->label('Approve Pending Commissions')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->visible(fn (Affiliate $record) => $record->commissions()->where('status', 'pending')->exists())
                    ->requiresConfirmation()
                    ->action(function (Affiliate $record) {
                        $record->commissions()->where('status', 'pending')->each(
                            fn ($c) => app(ReferralService::class)->approveCommission($c, auth()->id())
                        );
                    }),
                EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAffiliates::route('/'),
            'create' => Pages\CreateAffiliate::route('/create'),
            'edit'   => Pages\EditAffiliate::route('/{record}/edit'),
        ];
    }
}
