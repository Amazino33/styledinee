<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Username extends Model
{
    protected $primaryKey = 'username';
    public    $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['username', 'entity_type', 'entity_id'];

    /**
     * Check if a username is available across all entity types.
     */
    public static function isAvailable(string $username): bool
    {
        return ! static::where('username', $username)->exists();
    }

    /**
     * Claim a username for an entity. Throws a unique constraint exception if taken.
     */
    public static function claim(string $username, string $entityType, int $entityId): static
    {
        return static::create([
            'username'    => $username,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
        ]);
    }

    /**
     * Release a username (e.g. when an entity is deleted or changes username).
     */
    public static function release(string $username): void
    {
        static::where('username', $username)->delete();
    }

    /**
     * Transfer a username claim to a new username (atomic swap for username changes).
     */
    public static function transfer(string $oldUsername, string $newUsername): void
    {
        static::where('username', $oldUsername)->update(['username' => $newUsername]);
    }
}
