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
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Recipient Details
            $table->string('recipient_target')->index(); // Email, Phone, or User ID
            $table->string('recipient_name')->nullable();
            $table->string('user_id')->nullable()->index(); 

            // Content
            $table->string('channel')->index(); // 'email', 'sms', 'database'
            $table->string('subject');
            $table->text('body');
            $table->json('payload')->nullable(); // Extra UI metadata, action URLs, etc.

            // Status & Delivery Tracking
            $table->string('status')->default('pending')->index(); // 'pending', 'sent', 'failed'
            $table->text('error_message')->nullable();

            // Timestamps
            $table->timestamp('read_at')->nullable()->index(); // For in-app database notifications
            $table->timestamp('sent_at')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
