<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Account deletion support.
 *
 * Apple App Store guideline 5.1.1(v) and the Google Play "Data deletion"
 * policy both require an in-app way for an account holder to delete their
 * account. Deletion is a soft delete so order/invoice history stays intact
 * for the vendors and for tax record keeping, while the personal data on the
 * row is anonymised by AccountController::destroy().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('deletion_reason')->nullable()->after('status');
            $table->timestamp('anonymised_at')->nullable()->after('deletion_reason');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['deletion_reason', 'anonymised_at']);
        });
    }
};
