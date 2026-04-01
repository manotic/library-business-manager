<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wifi extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'mac',
        'amount',
        'is_debt',
        'expires_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_debt' => 'boolean',
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    /**
     * Helper to convert seconds to a readable format (H:i:s)
     */
    public function getFormattedTimeAttribute(): string
    {
        return gmdate('H:i:s', $this->time);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
