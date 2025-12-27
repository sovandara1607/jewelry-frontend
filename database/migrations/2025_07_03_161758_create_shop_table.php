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
        Schema::create('shop', function (Blueprint $table) {
            // Corresponds to Shop_id, bigint, auto-incrementing primary key
            $table->id('shop_id'); 

            // Corresponds to User_id, foreign key
            // Note: We'll assume a 'User' table with 'User_id' exists
            // The ->constrained() part would fail if the users table doesn't exist yet.
            // For just creating the column, you can use: $table->unsignedBigInteger('User_id');
            $table->foreignId('user_id')->constrained('users', 'id');

            // Corresponds to Shop_name, varchar(100)
            $table->string('shop_name', 100);

            // Corresponds to Shop_email, varchar(100)
            $table->string('shop_email', 100);

            // Corresponds to Shop_phoneNumber, varchar(20)
            $table->string('shop_phonenumber', 20);

            // Corresponds to Shop_address, varchar(255)
            $table->string('shop_address', 255);

            // Corresponds to Shop_description, text, can be null
            $table->text('shop_description')->nullable();

            // Corresponds to Shop_profilePic, varchar(255), can be null
            $table->string('shop_profilepic', 255)->nullable();
            
            // Corresponds to Date_created and Date_updated
            $table->dateTime('date_created');
            $table->dateTime('date_updated');

            // We remove the default timestamps() because you have custom date columns.
            // $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop');
    }
};