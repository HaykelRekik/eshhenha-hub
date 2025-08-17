<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            attributes: [
                'email' => 'admin@eshhanha.com',
            ],
            values: [
                'name' => 'Admin',
                'password' => '123123123',
            ]
        );

    }
}
