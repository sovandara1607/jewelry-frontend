<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     * Laravel expects 'id' by default. We must specify 'Product_id'.
     * @var string
     */
    protected $table = 'product';
    protected $primaryKey = 'product_id';

    /**
     * Indicates if the model should be timestamped.
     * We set this to true because you have timestamp columns.
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     * We must override the default 'created_at'.
     * @var string
     */
    const CREATED_AT = 'Date_created';

    /**
     * The name of the "updated at" column.
     * We must override the default 'updated_at'.
     * @var string
     */
    const UPDATED_AT = 'Date_updated';

    /**
     * The attributes that are mass assignable.
     * These MUST match your column names exactly.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_name',
        'product_category',
        'product_price',
        'product_description',
        'in_stock',
        'shop_id',
    ];

    /**
     * Get all of the images for the Product.
     */
    public function images()
    {
        return $this->hasMany(\App\Models\ProductImage::class, 'product_id', 'product_id');
    }
    /**
     * Get the shop that owns the product.
     */
    public function shop()
    {
        // This product belongs to a Shop.
        // We specify the foreign key 'shop_id' and the owner key 'shop_id'.
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id');
    }
    /**
     * Get the order item associated with this product if it has been sold.
     */
    public function orderItem()
    {
        // A product has one order item entry (since each listing is unique)
        return $this->hasOne(OrderItem::class, 'product_id');
    }
}