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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('contractor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contractor_address_id')->constrained()->cascadeOnDelete();
            $table->string('loading_address');
            $table->tinyInteger('status')->default(0);
            $table->unsignedInteger('freight_amount')->nullable(); // grosze
            $table->tinyInteger('currency')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
