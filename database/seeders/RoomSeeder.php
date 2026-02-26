<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Room::updateOrCreate(
            ['slug' => 'superior-king'],
            [
                'name' => 'Superior King',
                'description' => 'Premium king room with bay views and upgraded amenities.',
                'base_rate' => 6500.00,
                'is_active' => true,
            ]
        );

        Room::updateOrCreate(
            ['slug' => 'junior-suite'],
            [
                'name' => 'Junior Suite',
                'description' => 'Spacious suite with separate living area and premium finishes.',
                'base_rate' => 7200.00,
                'is_active' => true,
            ]
        );
    }
}
