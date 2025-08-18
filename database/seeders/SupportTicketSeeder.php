<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SupportTicket;
use Illuminate\Database\Seeder;

class SupportTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SupportTicket::factory(10)->create();
    }
}
