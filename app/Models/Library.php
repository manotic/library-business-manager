<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Library extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'is_debt',
        'debtor_name',
        'created_at',
    ];

    /**
     * Get the user that owns the library entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}