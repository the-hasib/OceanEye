<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('weather_updates', function (Blueprint $table) {
            $table->id();
            $table->string('temperature'); // ex:32°C
            $table->string('condition');   // ex: Sunny, Stormy
            $table->integer('signal_number')->default(0); // Danger Signal
            $table->string('message')->nullable(); // extra message
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_updates');
    }
};
