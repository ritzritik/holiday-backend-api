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
        Schema::create('ski_countries', function (Blueprint $table) {
            $table->id();
            $table->string('ski_country_api_id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('ski_resorts', function (Blueprint $table) {
            $table->id();
            $table->string('ski_resort_api_id');
            $table->foreignId('ski_countries_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('ski_countries');
        Schema::dropIfExists('ski_resorts');
    }
};
