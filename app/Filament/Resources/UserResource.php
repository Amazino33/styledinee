<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin');
    }

    public static function canCreate(): bool { return true; }
    public static function canEdit($record): bool { return true; }
    public static function canDelete($record): bool
    {
        // Prevent deleting yourself
        return $record->id !== auth()->id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(User::class, 'email', ignoreRecord: true),

                    TextInput::make('password')
                        ->password()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->minLength(8)
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->hint(fn (string $operation): ?string => $operation === 'edit'
                            ? 'Leave blank to keep current password'
                            : null
                        ),
                ])->columns(2),

            Section::make('Role')
                ->schema([
                    Select::make('role')
                        ->label('Role')
                        ->required()
                        ->options([
                            'admin'       => 'Admin',
                            'cashier'     => 'Cashier',
                            'tailor'      => 'Tailor',
                            'dry_cleaner' => 'Dry Cleaner',
                            'delivery'    => 'Delivery',
                        ])
                        ->default('cashier')
                        ->afterStateHydrated(function ($component, $record) {
                            if ($record) {
                                $component->state($record->roles->first()?->name);
                            }
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin'       => 'Admin',
                        'cashier'     => 'Cashier',
                        'tailor'      => 'Tailor',
                        'dry_cleaner' => 'Dry Cleaner',
                        'delivery'    => 'Delivery',
                        default       => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'admin'       => 'danger',
                        'cashier'     => 'primary',
                        'tailor'      => 'warning',
                        'dry_cleaner' => 'info',
                        'delivery'    => 'success',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin'       => 'Admin',
                        'cashier'     => 'Cashier',
                        'tailor'      => 'Tailor',
                        'dry_cleaner' => 'Dry Cleaner',
                        'delivery'    => 'Delivery',
                    ])
                    ->query(fn ($query, $data) => $data['value']
                        ? $query->role($data['value'])
                        : $query
                    ),
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
