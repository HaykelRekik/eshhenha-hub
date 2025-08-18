<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasAddresses;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasAddresses, HasFactory;

    protected $fillable = [
        'name',
        'responsible_name',
        'responsible_phone_number',
        'responsible_email',
        'company_id',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
