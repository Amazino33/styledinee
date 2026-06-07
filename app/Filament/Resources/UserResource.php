<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Staff';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string { return 'heroicon-o-user-group'; }
    public static function getNavigationGroup(): ?string { return 'Administration'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Account')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->required()->maxLength(255)
                        ->unique(User::class, 'email', ignoreRecord: true),
                    TextInput::make('password')
                        ->password()->minLength(8)
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->hint(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep current password' : null),
                    Select::make('roles')
                        ->label('Roles')->required()
                        ->multiple()
                        ->options([
                            'admin'       => 'Admin',
                            'cashier'     => 'Cashier',
                            'tailor'      => 'Tailor',
                            'embroidery'  => 'Embroidery',
                            'dry_cleaner' => 'Dry Cleaner',
                            'delivery'    => 'Delivery',
                            'printer'     => 'Printer',
                        ])
                        ->default(['cashier'])
                        ->afterStateHydrated(function ($component, $record) {
                            if ($record) {
                                $component->state($record->roles->pluck('name')->toArray());
                            }
                        }),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ])->columns(2),

            Section::make('Contact & Personal')
                ->schema([
                    TextInput::make('phone')->tel()->maxLength(20),
                    TextInput::make('address')->maxLength(255)->columnSpanFull(),
                    DatePicker::make('date_of_birth')->label('Date of Birth'),
                    Select::make('gender')
                        ->options(['male' => 'Male', 'female' => 'Female']),
                ])->columns(2)->collapsed(),

            Section::make('Employment')
                ->schema([
                    Select::make('employment_type')->label('Employment Type')
                        ->options([
                            'full_time'  => 'Full Time',
                            'part_time'  => 'Part Time',
                            'contract'   => 'Contract',
                            'freelance'  => 'Freelance',
                        ])->default('full_time'),
                    DatePicker::make('date_joined')->label('Date Joined'),
                ])->columns(2)->collapsed(),

            Section::make('Salary & Payment')
                ->schema([
                    Select::make('salary_type')->label('Salary Type')
                        ->options([
                            'monthly'   => 'Monthly',
                            'weekly'    => 'Weekly',
                            'per_piece' => 'Per Piece',
                        ])
                        ->default('monthly')
                        ->live(),
                    TextInput::make('salary_amount')->label('Salary Amount (₦)')
                        ->numeric()->prefix('₦')->default(0)
                        ->hint('Leave 0 for per-piece staff'),
                    TextInput::make('per_piece_rate')->label('Per Piece Rate (₦)')
                        ->numeric()->prefix('₦')->nullable()
                        ->visible(fn ($get) => $get('salary_type') === 'per_piece'),
                    TextInput::make('payment_day')->label('Payment Day')
                        ->numeric()->minValue(1)->maxValue(31)->nullable()
                        ->hint('Day of month (monthly) or day of week 1–7 (weekly)'),
                ])->columns(2)->collapsed(),

            Section::make('Bank Details')
                ->schema([
                    TextInput::make('bank_name')->label('Bank Name')->maxLength(100),
                    TextInput::make('account_number')->label('Account Number')->maxLength(20),
                    TextInput::make('account_name')->label('Account Name')->maxLength(150),
                ])->columns(2)->collapsed(),

            Section::make('Emergency Contact')
                ->schema([
                    TextInput::make('emergency_contact_name')->label('Name')->maxLength(150),
                    TextInput::make('emergency_contact_phone')->label('Phone')->tel()->maxLength(20),
                ])->columns(2)->collapsed(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin'       => 'Admin',
                        'cashier'     => 'Cashier',
                        'tailor'      => 'Tailor',
                        'embroidery'  => 'Embroidery',
                        'dry_cleaner' => 'Dry Cleaner',
                        'delivery'    => 'Delivery',
                        'printer'     => 'Printer',
                        default       => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'admin'       => 'danger',
                        'cashier'     => 'primary',
                        'tailor'      => 'warning',
                        'embroidery'  => 'info',
                        'dry_cleaner' => 'success',
                        'delivery'    => 'success',
                        'printer'     => 'gray',
                        default       => 'gray',
                    }),
                Tables\Columns\TextColumn::make('employment_type')->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? '')))
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Joined')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin'       => 'Admin',
                        'cashier'     => 'Cashier',
                        'tailor'      => 'Tailor',
                        'embroidery'  => 'Embroidery',
                        'dry_cleaner' => 'Dry Cleaner',
                        'delivery'    => 'Delivery',
                        'printer'     => 'Printer',
                    ])
                    ->query(fn ($query, $data) => $data['value'] ? $query->role($data['value']) : $query),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->placeholder('All staff'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
