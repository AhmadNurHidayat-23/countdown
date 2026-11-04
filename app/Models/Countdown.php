<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Countdown extends Model
{
    protected $fillable = ['name', 'description', 'target_at'];

    protected $hidden = ['manage_token'];

    protected static function booted(): void
    {
        static::creating(function (Countdown $countdown): void {
            $countdown->share_token = Str::random(48);
            $countdown->manage_token = Str::random(48);
        });
    }

    protected function casts(): array
    {
        return ['target_at' => 'immutable_datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'share_token';
    }
}
