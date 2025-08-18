<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('regions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $saudiArabia = Country::query()
            ->where('iso_code', 'SA')
            ->value('id');

        $regions = [
            ['name_en' => 'Riyadh', 'name_ar' => 'الرياض', 'name_ur' => 'ریاض', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Makkah', 'name_ar' => 'مكة المكرمة', 'name_ur' => 'مکہ', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Madinah', 'name_ar' => 'المدينة المنورة', 'name_ur' => 'مدینہ', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Qassim', 'name_ar' => 'القصيم', 'name_ur' => 'قصیم', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Eastern Province', 'name_ar' => 'الشرقية', 'name_ur' => 'شرقی صوبہ', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Asir', 'name_ar' => 'عسير', 'name_ur' => 'عسیر', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Tabuk', 'name_ar' => 'تبوك', 'name_ur' => 'تبوک', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Hail', 'name_ar' => 'حائل', 'name_ur' => 'حائل', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Northern Borders', 'name_ar' => 'الحدود الشمالية', 'name_ur' => 'شمالی سرحد', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Jazan', 'name_ar' => 'جازان', 'name_ur' => 'جازان', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Najran', 'name_ar' => 'نجران', 'name_ur' => 'نجران', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Bahah', 'name_ar' => 'الباحة', 'name_ur' => 'باحہ', 'is_active' => true, 'country_id' => $saudiArabia],
            ['name_en' => 'Jawf', 'name_ar' => 'الجوف', 'name_ur' => 'الجوف', 'is_active' => true, 'country_id' => $saudiArabia],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate([
                'name_en' => $region['name_en'],
            ], $region);
        }
    }
}
