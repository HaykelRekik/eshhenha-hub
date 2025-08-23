<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            attributes: [
                'email' => 'admin@eshhanha.com',
            ],
            values: [
                'name' => 'Admin',
                'password' => '123123123',
                'role' => 'admin',
                'is_active' => true,
                'national_id' => '00000000',
                'phone_number' => '+966 500010001',
            ]
        );
    }
}
