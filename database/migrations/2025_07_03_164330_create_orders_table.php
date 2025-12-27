<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The first argument is the table name, which is 'Orders' in your case.
        Schema::create('orders', function (Blueprint $table) {
            // Corresponds to Order_id, bigint, auto-incrementing primary key
            $table->id('order_id');

            // Corresponds to User_id, foreign key. Assumes a 'User' table exists with a 'User_id' primary key.
            $table->foreignId('user_id')->constrained('users', 'id');

            // Corresponds to Order_date, datetime
            $table->dateTime('order_date');

            // Corresponds to Total_amount, decimal(10,2)
            $table->decimal('total_amount', 10, 2);

            // Corresponds to status, varchar(20). Let's add a default value.
            $table->enum('status', ['Pending', 'Confirmed', 'Rejected'])->default('Pending');

            // Corresponds to Delivery_address, varchar(255)
            $table->string('delivery_address', 255);

            // Corresponds to your custom-named timestamp columns
            $table->dateTime('date_created');
            $table->dateTime('date_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};