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
        Schema::table('sos_alerts', function (Blueprint $table) {
            // Add 'boat_id' to link the SOS alert to a specific boat
            $table->foreignId('boat_id')->nullable()->constrained('boats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            //
        });
    }
};
