<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Push notification device registrations (FCM / APNs-via-FCM).
 *
 * The token is unique on its own: when a device is handed to another account
 * FCM reissues the same token to the new login, so registering it must move
 * the row rather than create a duplicate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('token', 512);
            $table->enum('platform', ['android', 'ios', 'web'])->default('android');
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique('token');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
    }
};
