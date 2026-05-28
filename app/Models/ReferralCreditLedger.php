<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCreditLedger extends Model
{
    protected $table = 'referral_credit_ledger';

    protected $fillable = [
        'owner_username', 'owner_type', 'owner_entity_id',
        'type', 'amount', 'description',
        'reference_type', 'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // ── Balance helpers ────────────────────────────────────────────────────────

    public static function balanceFor(string $username): float
    {
        $credits = static::where('owner_username', $username)->where('type', 'credit')->sum('amount');
        $debits  = static::where('owner_username', $username)->where('type', 'debit')->sum('amount');

        return max(0, (float) $credits - (float) $debits);
    }

    public static function credit(
        string $username,
        string $ownerType,
        int    $ownerEntityId,
        float  $amount,
        string $description,
        string $referenceType = null,
        int    $referenceId = null,
    ): static {
        return static::create([
            'owner_username'  => $username,
            'owner_type'      => $ownerType,
            'owner_entity_id' => $ownerEntityId,
            'type'            => 'credit',
            'amount'          => $amount,
            'description'     => $description,
            'reference_type'  => $referenceType,
            'reference_id'    => $referenceId,
        ]);
    }

    public static function debit(
        string $username,
        string $ownerType,
        int    $ownerEntityId,
        float  $amount,
        string $description,
        string $referenceType = null,
        int    $referenceId = null,
    ): static {
        return static::create([
            'owner_username'  => $username,
            'owner_type'      => $ownerType,
            'owner_entity_id' => $ownerEntityId,
            'type'            => 'debit',
            'amount'          => $amount,
            'description'     => $description,
            'reference_type'  => $referenceType,
            'reference_id'    => $referenceId,
        ]);
    }
}
