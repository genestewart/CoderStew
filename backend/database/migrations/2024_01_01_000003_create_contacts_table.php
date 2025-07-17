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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('subject');
            $table->longText('message');
            $table->enum('type', ['general', 'project', 'support', 'partnership'])->default('general');
            $table->enum('status', ['unread', 'read', 'responded'])->default('unread');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('source')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('email');
            $table->index('type');
            $table->index('status');
            $table->index('priority');
            $table->index('source');
            $table->index('responded_at');
            $table->index('responded_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
