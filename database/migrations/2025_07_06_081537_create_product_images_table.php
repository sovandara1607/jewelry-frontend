<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id('image_id'); // A unique ID for each image record
            
            // This is the foreign key linking back to the product table
            $table->foreignId('product_id')->constrained('product', 'product_id')->onDelete('cascade');
            
            $table->string('image_path'); // The path to the image file, e.g., 'product-images/abc.jpg'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};