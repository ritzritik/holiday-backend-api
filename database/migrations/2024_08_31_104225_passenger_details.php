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
        Schema::create('passenger_details', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id');
            $table->enum('title', ['Mr', 'Ms', 'Mrs'])->nullable();
            $table->string('first_name');
            $table->string('surname');
            $table->string('email');
            $table->string('payment_status')->nullable();
            $table->string('contact_number');
            $table->string('package_type')->nullable();
            $table->decimal('price', 8, 2);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passenger_details');
    }
};

