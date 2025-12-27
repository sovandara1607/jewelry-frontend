<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: The table name in the screenshot is singular. We'll match it.
        Schema::create('orderitem', function (Blueprint $table) {
            // Corresponds to OrderItem_id, bigint, auto-incrementing primary key
            $table->id('orderitem_id');

            // Corresponds to Order_id, foreign key referencing the Orders table
            $table->foreignId('order_id')->constrained('orders', 'order_id');
            
            // Corresponds to Product_id, foreign key referencing the Product table
            $table->foreignId('product_id')->constrained('product', 'product_id');

            // Corresponds to Quantity, int(11)
            $table->integer('quantity');

            // Corresponds to Price, decimal(10,2). This stores the price at the time of purchase.
            $table->decimal('price', 10, 2);

            // Corresponds to your custom-named timestamp columns
            $table->dateTime('date_created');
            $table->dateTime('date_updated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orderitem');
    }
};