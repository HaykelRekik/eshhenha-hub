<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\UserRole;
use App\Models\Warehouse;

class WarehouseObserver
{
    public function creating(Warehouse $warehouse): void
    {
        if (auth()->check() && auth()->user()->hasRole(UserRole::COMPANY)) {
            $warehouse->company_id = auth()->user()->company->id;
        }
    }
}
