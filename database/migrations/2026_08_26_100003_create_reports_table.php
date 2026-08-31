<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Abuse reports.
 *
 * App Store guideline 1.2 requires a way to report offensive user generated
 * content (chat messages, product listings, reviews and profiles here) and
 * evidence that reports are acted on — the admin panel's Reports screen is
 * that evidence.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();

            // What is being reported. reportable_id is not a real FK because it
            // points at a different table per type.
            $table->enum('reportable_type', ['user', 'product', 'message', 'review']);
            // A string, not an integer: Chatify message ids are UUIDs.
            $table->string('reportable_id', 64);

            $table->string('reason');
            $table->text('details')->nullable();
            $table->enum('status', ['pending', 'reviewing', 'actioned', 'dismissed'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['reportable_type', 'reportable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
