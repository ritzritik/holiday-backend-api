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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id')->default('100');
            $table->string('package_code')->default('PKG-101');
            $table->string('package_name')->default('HOLSEARCH');
            $table->string('depart')->default('LGW|STN|LHR|LCY|SEN|LTN');
            $table->unsignedBigInteger('countryid');
            $table->unsignedBigInteger('regionid');
            $table->unsignedBigInteger('areaid');
            $table->unsignedBigInteger('resortid');
            $table->date('depdate')->default(now());
            $table->integer('adults');
            $table->integer('children');
            $table->integer('duration');
            $table->decimal('price', 8, 2);
            $table->tinyInteger('is_deleted')->default(0);
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
