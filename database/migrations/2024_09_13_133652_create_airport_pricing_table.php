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
        Schema::create('airport_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_id')->constrained('airports')->onDelete('cascade');
            $table->decimal('transfer_price', 8, 2);
            $table->decimal('parking_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airport_pricing');
    }
};
