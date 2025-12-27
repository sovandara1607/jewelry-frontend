<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        // The first argument is the table name, which is 'Product' in your case.
        Schema::create('product', function (Blueprint $table) {
            // Corresponds to Product_id, bigint, auto-incrementing primary key
            $table->id('product_id');

            // Corresponds to Product_name, varchar(100)
            $table->string('product_name', 100);

            // Corresponds to Product_category, varchar(50)
            $table->string('product_category', 50);

            // Corresponds to Product_price, decimal(10,2)
            $table->decimal('product_price', 10, 2);

            // Corresponds to Product_description, text, can be null
            $table->text('product_description')->nullable();

            // Corresponds to Product_images, varchar(255), can be null
            $table->string('product_images', 255)->nullable();

            // Corresponds to In_stock, int(11), with a default value of 1
            $table->integer('in_stock')->default(1);

            // Corresponds to Shop_id, foreign key. Assumes a 'Shop' table exists.
            $table->foreignId('shop_id')->constrained('shop', 'shop_id');

            // Corresponds to your custom-named timestamp columns
            $table->dateTime('date_created');
            $table->dateTime('date_updated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};