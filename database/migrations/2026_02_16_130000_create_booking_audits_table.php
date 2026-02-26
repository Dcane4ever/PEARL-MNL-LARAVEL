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
        Schema::create('booking_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_audits');
    }
};
