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
        Schema::create('delivery_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_transport_set_id')->nullable()->constrained()->cascadeOnDelete();
            $table->tinyInteger('type');
            $table->unsignedInteger('amount'); // grosze
            $table->tinyInteger('currency');
            $table->string('description')->nullable();

            $table->timestamps();

            $table->index('delivery_id');
            $table->index('delivery_transport_set_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_costs');
    }
};
