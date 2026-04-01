<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wifis()
    {
        return $this->hasMany(Wifi::class);
    }

    public function libraries()
    {
        return $this->hasMany(Library::class);
    }

    public function accessories()
    {
        return $this->hasMany(Accessory::class);
    }

    public function lendings()
    {
        return $this->hasMany(Lending::class);
    }

    public function outIncomes()
    {
        return $this->hasMany(OutIncome::class);
    }
    
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
