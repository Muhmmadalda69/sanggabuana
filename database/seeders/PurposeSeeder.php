<?php

namespace Database\Seeders;

use App\Models\Purpose;
use Illuminate\Database\Seeder;

class PurposeSeeder extends Seeder
{
    public function run(): void
    {
        Purpose::updateOrCreate(
            ['slug' => 'hiking'],
            [
                'name' => 'Hiking',
                'is_all_destinations' => true,
            ]
        );

        Purpose::updateOrCreate(
            ['slug' => 'trail-run'],
            [
                'name' => 'Trail Run',
                'is_all_destinations' => true,
            ]
        );

        Purpose::updateOrCreate(
            ['slug' => 'ziarah'],
            [
                'name' => 'Ziarah',
                'is_all_destinations' => true,
            ]
        );
    }
}
