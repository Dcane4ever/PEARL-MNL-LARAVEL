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
            $table->unsignedTinyInteger('adults')->default(2)->after('room_id');
            $table->unsignedTinyInteger('children')->default(0)->after('adults');
            $table->unsignedTinyInteger('rooms_count')->default(1)->after('children');

            $table->string('id_document_path')->nullable()->after('status');
            $table->timestamp('id_document_uploaded_at')->nullable()->after('id_document_path');
            $table->foreignId('verified_by_admin_id')->nullable()->after('id_document_uploaded_at')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by_admin_id');
            $table->text('verification_notes')->nullable()->after('verified_at');

            $table->index(['status', 'check_in_date']);
            $table->index('verified_by_admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['status', 'check_in_date']);
            $table->dropIndex(['verified_by_admin_id']);

            $table->dropConstrainedForeignId('verified_by_admin_id');
            $table->dropColumn([
                'adults',
                'children',
                'rooms_count',
                'id_document_path',
                'id_document_uploaded_at',
                'verified_at',
                'verification_notes',
            ]);
        });
    }
};
