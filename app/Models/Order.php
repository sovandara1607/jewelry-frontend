<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'order_id';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     */
    const CREATED_AT = 'date_created';

    /**
     * The name of the "updated at" column.
     */
    const UPDATED_AT = 'date_updated';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'order_date',
        'total_amount',
        'status',
        'delivery_address',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the user that placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the items for the order.
     */
    public function items()
    {
        // Specify both foreign key and local key for proper relationship
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }
}