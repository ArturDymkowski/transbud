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
        Schema::create('delivery_transport_set_status_history', function (Blueprint $table) {
            $table->id();
            // Explicit short constraint name: the auto-generated one (table_column_foreign)
            // exceeds MySQL's 64-character identifier limit for this table/column pair.
            $table->foreignId('delivery_transport_set_id')
                ->constrained('delivery_transport_sets', indexName: 'delivery_transport_set_status_history_transport_set_id_foreign')
                ->cascadeOnDelete();
            $table->tinyInteger('status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_transport_set_status_history');
    }
};
