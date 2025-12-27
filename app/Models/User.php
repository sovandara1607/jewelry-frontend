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
        'phonenumber',   
        'profilepic', 
        'address',
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

    /**
     * Get the shop associated with the user.
     * A user can have one shop.
     */
    public function shop()
    {
        // Because your foreign key 'User_id' on the Shop table matches the
        // primary key 'User_id' on this User table, this simple relationship works.
        // If they were different, you would need to specify the keys.
        return $this->hasOne(Shop::class, 'user_id');
    }

    public function orders()
{
    return $this->hasMany(Order::class, 'user_id'); // Assuming you have an Order model
}
}
