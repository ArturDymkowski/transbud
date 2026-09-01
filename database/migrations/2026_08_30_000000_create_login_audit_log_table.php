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
        Schema::create('login_audit_log', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->boolean('successful');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            // Set only on successful logins, so the matching Logout event (fired by the
            // same browser session) can find and close this exact row — see
            // App\Listeners\LogUserLogout. Irrelevant/left null for failed attempts.
            $table->string('session_id')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('email');
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_audit_log');
    }
};
