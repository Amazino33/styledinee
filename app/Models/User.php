<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        // Contact
        'phone', 'address',
        // Personal
        'date_of_birth', 'gender',
        // Employment
        'employment_type', 'date_joined', 'is_active',
        // Salary
        'salary_type', 'salary_amount', 'per_piece_rate', 'payment_day',
        // Banking
        'bank_name', 'account_number', 'account_name',
        // Emergency
        'emergency_contact_name', 'emergency_contact_phone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth'     => 'date',
            'date_joined'       => 'date',
            'is_active'         => 'boolean',
            'salary_amount'     => 'decimal:2',
            'per_piece_rate'    => 'decimal:2',
        ];
    }

    // ── Filament access ────────────────────────────────────────────────────────

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) return false;

        return $this->roles()->whereIn('name', [
            'admin', 'cashier', 'tailor', 'embroidery',
            'dry_cleaner', 'delivery', 'printer',
        ])->exists();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function needsUsername(): bool
    {
        return empty($this->username);
    }

    public function creditBalance(): float
    {
        if (! $this->username) return 0.0;

        return ReferralCreditLedger::balanceFor($this->username);
    }

    // ── Relationships ──────────────────────────────────────────────────────────

    public function customerProfile()
    {
        return $this->hasOne(Customer::class);
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function orderAssignments()
    {
        return $this->hasMany(OrderAssignment::class, 'assigned_to');
    }

    public function assignedOrders()
    {
        return $this->hasMany(OrderAssignment::class, 'assigned_by');
    }
}
