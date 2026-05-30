<?php

namespace App\Filament\Resources\AffiliateResource\Pages;

use App\Filament\Resources\AffiliateResource;
use App\Models\Customer;
use App\Models\User;
use App\Models\Username;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;

class CreateAffiliate extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = AffiliateResource::class;

    protected function getSteps(): array
    {
        return [

            // ── Step 1: Who is this? ────────────────────────────────────────
            Step::make('Who is this affiliate?')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Radio::make('affiliate_type')
                        ->label('Affiliate type')
                        ->options([
                            'new'               => 'New person (not yet in the system)',
                            'existing_customer' => 'Existing customer',
                            'existing_user'     => 'Existing staff member',
                        ])
                        ->default('new')
                        ->required()
                        ->live()
                        ->descriptions([
                            'new'               => 'Creates a brand-new affiliate record with their own login details.',
                            'existing_customer' => 'Links the affiliate to a customer who already has an account.',
                            'existing_user'     => 'Links the affiliate to a staff member (user) in the system.',
                        ]),
                ]),

            // ── Step 2: Details (conditional on type) ──────────────────────
            Step::make('Affiliate Details')
                ->icon('heroicon-o-identification')
                ->schema([

                    // ─ Existing customer selector ─
                    Select::make('customer_id')
                        ->label('Select Customer')
                        ->options(
                            Customer::whereNotNull('username')
                                ->whereDoesntHave('affiliate')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(fn ($get) => $get('affiliate_type') === 'existing_customer')
                        ->visible(fn ($get) => $get('affiliate_type') === 'existing_customer')
                        ->live()
                        ->afterStateUpdated(function (?int $state, Set $set) {
                            if (! $state) return;
                            $c = Customer::find($state);
                            if (! $c) return;
                            $set('name',     $c->name);
                            $set('email',    $c->email ?? '');
                            $set('phone',    $c->phone ?? '');
                            $set('username', $c->username ?? '');
                        }),

                    // ─ Existing user selector ─
                    Select::make('user_id')
                        ->label('Select Staff Member')
                        ->options(
                            User::whereDoesntHave('affiliate')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->required(fn ($get) => $get('affiliate_type') === 'existing_user')
                        ->visible(fn ($get) => $get('affiliate_type') === 'existing_user')
                        ->live()
                        ->afterStateUpdated(function (?int $state, Set $set) {
                            if (! $state) return;
                            $u = User::find($state);
                            if (! $u) return;
                            $set('name',     $u->name);
                            $set('email',    $u->email ?? '');
                            $set('phone',    $u->phone ?? '');
                            $set('username', $u->username ?? '');
                        }),

                    // ─ Name (shared, visible for all types) ─
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label(fn ($get) => in_array($get('affiliate_type'), ['existing_customer','existing_user'])
                            ? 'Name (auto-filled)' : 'Full Name'),

                    // ─ Email & Phone (new only) ─
                    TextInput::make('email')
                        ->email()->nullable()
                        ->visible(fn ($get) => $get('affiliate_type') !== 'existing_customer'
                            && $get('affiliate_type') !== 'existing_user'),

                    TextInput::make('phone')
                        ->tel()->nullable()
                        ->visible(fn ($get) => $get('affiliate_type') !== 'existing_customer'
                            && $get('affiliate_type') !== 'existing_user'),

                    // ─ Username (shared, auto-filled for existing) ─
                    TextInput::make('username')
                        ->required()
                        ->maxLength(50)
                        ->alphaDash()
                        ->unique('affiliates', 'username')
                        ->hint(fn ($get) => in_array($get('affiliate_type'), ['existing_customer','existing_user'])
                            ? 'Pre-filled from their account — change only if needed.'
                            : 'This becomes their referral code.'),
                ]),

            // ── Step 3: Commission & Payout ────────────────────────────────
            Step::make('Commission & Payout')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    TextInput::make('commission_rate')
                        ->label('Commission Rate (%)')
                        ->numeric()->minValue(0)->maxValue(100)->nullable()
                        ->placeholder('Leave blank to use global default')
                        ->suffix('%'),

                    Select::make('referral_payout_type')
                        ->label('One-time Referral Reward Payout')
                        ->options(['credit' => 'Account Credit', 'bank_transfer' => 'Bank Transfer'])
                        ->default('credit')->required(),

                    Select::make('affiliate_payout_type')
                        ->label('Recurring Commission Payout')
                        ->options(['credit' => 'Account Credit', 'bank_transfer' => 'Bank Transfer'])
                        ->default('bank_transfer')->required(),
                ])->columns(3),

            // ── Step 4: Bank Details ────────────────────────────────────────
            Step::make('Bank Details')
                ->icon('heroicon-o-building-library')
                ->description('Required only if payout method is Bank Transfer.')
                ->schema([
                    TextInput::make('bank_name')->maxLength(100),
                    TextInput::make('account_number')->maxLength(20),
                    TextInput::make('account_name')->maxLength(150),
                ])->columns(3),

            // ── Step 5: Status & Notes ─────────────────────────────────────
            Step::make('Status & Notes')
                ->icon('heroicon-o-clipboard-document-check')
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending'   => 'Pending (requires approval)',
                            'active'    => 'Active immediately',
                            'suspended' => 'Suspended',
                        ])
                        ->default('pending')
                        ->required(),

                    Textarea::make('notes')
                        ->label('Internal Notes')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Optional notes about this affiliate...'),
                ])->columns(2),
        ];
    }

    protected function afterCreate(): void
    {
        Username::claim($this->record->username, 'affiliate', $this->record->id);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the helper field — not a DB column
        unset($data['affiliate_type']);

        return $data;
    }
}
