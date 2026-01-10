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
            $table->string('name'); // Stores Full Name or Officer Name

            // Email is optional for fishermen
            $table->string('email')->unique()->nullable();

            // Mobile number for fishermen
            $table->string('mobile')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // --- Role Management ---
            // Distinguishes between 'fisherman' and 'coast_guard'
            $table->string('role')->default('fisherman');

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
