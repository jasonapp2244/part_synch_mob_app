<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repoint reviews.vendor_id at the vendor's user row.
 *
 * The original create_reviews_table migration constrained vendor_id against
 * `vendor_type` — the lookup table of vendor *categories* (Retailer,
 * Wholesaler, ...), not the vendor themself. Everywhere else in the codebase
 * a vendor_id is a users.id: orders.vendor_id is set from User::find(), and
 * products belong to a vendor through user_id.
 *
 * The effect was that saving any review whose vendor_id was a real vendor
 * failed with a foreign key violation, unless that vendor's id happened to
 * collide with one of the 14 vendor_type rows. That is why the reviews table
 * was still empty.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        // Any row that survived pointed at a vendor_type id, which is
        // meaningless as a vendor reference — null it rather than migrate it
        // to a wrong user.
        if ($this->constraintExists('reviews_vendor_id_foreign')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropForeign('reviews_vendor_id_foreign');
            });

            DB::table('reviews')->update(['vendor_id' => null]);
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')->on('vendor_type')
                ->cascadeOnDelete();
        });
    }

    private function constraintExists(string $name): bool
    {
        return ! empty(DB::select(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            ['reviews', $name]
        ));
    }
};
