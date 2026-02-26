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
        Schema::create('daily_room_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->date('inventory_date');
            $table->unsignedSmallInteger('available_rooms')->default(0);
            $table->timestamps();

            $table->unique(['floor_id', 'room_id', 'inventory_date'], 'daily_floor_room_inventory_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_room_inventories');
    }
};
