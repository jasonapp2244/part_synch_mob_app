<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    protected $fillable = [
        'blocker_id',
        'blocked_id',
        'reason',
    ];

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocker_id');
    }

    public function blocked()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }

    /**
     * Is there a block in either direction between two users?
     *
     * Blocking is symmetric in effect: whoever pressed the button, neither
     * side may message the other afterwards.
     */
    public static function existsBetween($a, $b): bool
    {
        return static::where(function ($q) use ($a, $b) {
            $q->where('blocker_id', $a)->where('blocked_id', $b);
        })->orWhere(function ($q) use ($a, $b) {
            $q->where('blocker_id', $b)->where('blocked_id', $a);
        })->exists();
    }

    /**
     * Every user id that $userId may not interact with, in either direction.
     */
    public static function idsFor($userId): array
    {
        $blocked = static::where('blocker_id', $userId)->pluck('blocked_id');
        $blockedBy = static::where('blocked_id', $userId)->pluck('blocker_id');

        return $blocked->merge($blockedBy)->unique()->values()->all();
    }
}
