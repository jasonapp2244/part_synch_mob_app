<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
       public function up(): void {
        Schema::create('vendor_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('boost_packages')->onDelete('cascade');
            $table->foreignId('boost_position_id')->nullable()->constrained('boost_positions')->nullOnDelete();
            $table->decimal('amount',10,2)->default(0.00);
            $table->string('currency',10)->default('usd');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->enum('payment_status',['pending','success','failed','refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // stripe
            $table->string('transaction_id')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vendor_boosts'); }
};
