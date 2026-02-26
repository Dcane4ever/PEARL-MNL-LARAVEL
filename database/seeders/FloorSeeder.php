<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($floor = 1; $floor <= 20; $floor++) {
            Floor::updateOrCreate(
                ['number' => $floor],
                ['label' => 'Floor '.$floor]
            );
        }
    }
}
