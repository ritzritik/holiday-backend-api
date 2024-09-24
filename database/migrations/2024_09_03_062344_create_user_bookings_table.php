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
        Schema::create('user_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to the users table
            $table->string('booking_reference')->unique();
            $table->string('booking_type'); // e.g., 'Flight', 'Hotel', 'Package', 'Holiday'
            $table->string('supplier_name')->nullable(); // e.g., Airline, Hotel chain
            $table->string('supplier_code')->nullable();
            $table->string('booking_details'); // e.g., 'London to New York, 15th Aug 2024, British Airways'
            $table->decimal('price', 10, 2)->default(0.00); // Price in GBP (£)
            $table->string('currency')->default('GBP'); // Currency code, default GBP
            $table->date('check_in_date')->nullable(); // Applicable for hotels/packages
            $table->date('check_out_date')->nullable(); // Applicable for hotels/packages
            $table->integer('stay_duration')->nullable(); // Number of nights or days
            $table->string('room_type')->nullable(); // For hotel bookings
            $table->string('board_basis')->nullable(); // e.g., BB, HB, AI
            $table->string('star_rating')->nullable(); // For hotels/packages
            $table->string('booking_status')->default('confirmed'); // e.g., confirmed, cancelled
            $table->text('additional_information')->nullable(); // Any additional information
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bookings');
    }
};
