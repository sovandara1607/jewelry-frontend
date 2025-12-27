<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * We specify 'shop' (singular) because Laravel expects 'shops' (plural).
     * @var string
     */
    protected $table = 'shop';

    /**
     * The primary key associated with the table.
     * We must specify this because it is 'shop_id' and not the default 'id'.
     * @var string
     */
    protected $primaryKey = 'shop_id';

    /**
     * Indicates if the model should be timestamped.
     * We set this to true because you have timestamp columns.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     * @var string
     */
    const CREATED_AT = 'date_created';

    /**
     * The name of the "updated at" column.
     * @var string
     */
    const UPDATED_AT = 'date_updated';

    /**
     * The attributes that are mass assignable.
     * These must match your new snake_case column names.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_email',
        'shop_phonenumber',
        'shop_address',
        'shop_description',
        'shop_profilepic',
    ];

    /**
     * Get the user that owns the shop.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the products for the shop.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }
}