<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Traits\HasActiveScope;
use App\Traits\HasAddresses;
use App\Traits\HasRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
// use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    /** @use HasFactory<UserFactory> */
    use HasActiveScope, HasAddresses, HasFactory, HasRole, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'national_id',
        'avatar_url',
        'loyalty_points',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'referral_code',
        'referred_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    //    public function wallet(): HasOne
    //    {
    //        return $this->hasOne(Wallet::class, 'user_id');
    //    }

    public function defaultAddress(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('is_default', true);
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'user_id');
    }

    //    public function warehouses(): HasManyThrough
    //    {
    //        return $this->hasManyThrough(
    //            related: Warehouse::class,
    //            through: Company::class,
    //            firstKey: 'user_id',
    //            secondKey: 'company_id',
    //            localKey: 'id',
    //            secondLocalKey: 'id'
    //        );
    //    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        return $this->{$avatarColumn} ? Storage::disk('public')->url($this->{$avatarColumn}) : null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    //    protected function balance(): Attribute
    //    {
    //        return Attribute::make(
    //            get: fn (): float => $this->wallet()->balance ?? 0,
    //        );
    //    }
}
// Test comment
