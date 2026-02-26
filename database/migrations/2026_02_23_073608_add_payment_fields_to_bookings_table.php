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
       Schema::table('bookings', function (Blueprint $table) {
    $table->string('payment_status')->default('unpaid')->after('status');
    $table->string('payment_proof_path')->nullable()->after('payment_status');
    $table->timestamp('payment_submitted_at')->nullable()->after('payment_proof_path');
    $table->timestamp('payment_verified_at')->nullable()->after('payment_submitted_at');
    $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_verified_at');
    $table->text('payment_notes')->nullable()->after('payment_verified_by');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('bookings', function (Blueprint $table) {
    $table->dropColumn([
        'payment_status',
        'payment_proof_path',
        'payment_submitted_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_notes',
    ]);
});
    }
};
