<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([1, 2, 3] as $slot) {
            Facility::updateOrCreate(
                ['slot' => $slot],
                ['caption' => null]
            );
        }
    }
}
