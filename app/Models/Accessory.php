<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accessory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'accessory_name',
        'buying_amount',
        'selling_amount',
        'paid_amount',
        'name',
        'contact',
    ];

    /**
     * Get the user that owns the accessory record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}