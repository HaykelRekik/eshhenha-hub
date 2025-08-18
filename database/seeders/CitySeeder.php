<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('cities.json');

        if ( ! File::exists($jsonPath)) {
            $this->command->error("Cities JSON file not found at: {$jsonPath}");

            return;
        }

        $jsonData = File::get($jsonPath);
        $cities = json_decode($jsonData, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->command->error('Invalid JSON format in cities file');

            return;
        }

        $dataToInsert = array_map(fn (array $city): array => [
            'name_ar' => $city['name_ar'],
            'name_en' => $city['name_en'],
            'name_ur' => $city['name_ar'], // Set name_ur same as name_ar
            'region_id' => $city['region_id'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ], $cities);

        $chunks = array_chunk($dataToInsert, 500);

        foreach ($chunks as $chunk) {
            City::insert($chunk);
        }

    }
}
