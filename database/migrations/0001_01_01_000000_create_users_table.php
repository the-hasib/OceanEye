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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- Role Management ---
            $table->string('role')->default('fisherman');

            // --- Status Management (Added Step) ---
            // pending = waiting for admin approval
            // approved = can login
            $table->string('status')->default('pending');

            // --- Fisherman Specific Fields ---
            $table->string('license_no')->nullable();
            $table->string('nid')->nullable();
            $table->string('address')->nullable();

            // --- Coast Guard Specific Fields ---
            $table->string('service_id')->nullable();
            $table->string('station_zone')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
