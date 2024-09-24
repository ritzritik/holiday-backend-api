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
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_booking_id')->nullable()->constrained('user_bookings')->onDelete('cascade'); // Make nullable
            $table->string('voucher_code')->unique();
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('currency')->default('GBP');
            $table->date('expiry_date');
            $table->text('terms_and_conditions')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};
