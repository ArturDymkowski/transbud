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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('pesel', 11)->unique();

            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('street_nr')->nullable();
            $table->string('home_nr')->nullable();
            $table->longText('extra_info')->nullable();

            $table->string('driving_license_number')->unique();
            $table->date('license_expiry_date');
            $table->date('medical_exam_valid_until')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
