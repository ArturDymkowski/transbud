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
        Schema::create('delivery_goods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_transport_set_id')->constrained()->cascadeOnDelete();
            $table->foreignId('good_id')->nullable()->constrained('goods')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('quantity', 12, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_goods');
    }
};
