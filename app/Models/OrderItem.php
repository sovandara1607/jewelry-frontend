<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'orderitem';

    protected $primaryKey = 'orderitem_id';

    public $timestamps = true;

    const CREATED_AT = 'date_created';

    const UPDATED_AT = 'date_updated';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the order that this item belongs to.
     */
    public function order()
    {
        // Specify both foreign key and owner key for proper relationship
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the product associated with this order item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}