<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('checkout_release_state', 32)
                ->nullable()
                ->after('status');
            $table->timestamp('checkout_release_available_at')
                ->nullable()
                ->after('checkout_release_state');
            $table->timestamp('checkout_released_at')
                ->nullable()
                ->after('checkout_release_available_at');
            $table->foreignId('checkout_release_admin_id')
                ->nullable()
                ->after('checkout_released_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['status', 'check_out_date'], 'bookings_status_checkout_date_idx');
            $table->index('checkout_release_state', 'bookings_checkout_release_state_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_status_checkout_date_idx');
            $table->dropIndex('bookings_checkout_release_state_idx');
            $table->dropConstrainedForeignId('checkout_release_admin_id');
            $table->dropColumn([
                'checkout_release_state',
                'checkout_release_available_at',
                'checkout_released_at',
            ]);
        });
    }
};
