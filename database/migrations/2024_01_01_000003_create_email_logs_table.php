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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('email_campaigns')->onDelete('cascade');
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->string('subject');
            $table->text('content');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced', 'spam'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('message_id')->nullable(); // For tracking delivery
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('headers')->nullable(); // Store email headers for anti-spam measures
            $table->timestamps();
            
            $table->index(['to_email', 'status']);
            $table->index(['campaign_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
}; 