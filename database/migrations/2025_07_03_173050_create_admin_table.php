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
        Schema::create('admin', function (Blueprint $table) {
            // Corresponds to Admin_id, bigint, auto-incrementing primary key
            $table->id('admin_id');

            // Corresponds to Admin_username, varchar(50), with a unique index
            $table->string('admin_username', 50)->unique();

            // Corresponds to Admin_email, varchar(100), with a unique index
            $table->string('admin_email', 100)->unique();

            // Corresponds to Admin_password, varchar(255)
            $table->string('admin_password');

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
        Schema::dropIfExists('admin');
    }
};