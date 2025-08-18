<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class UserLoginListener
{
    public function __construct() {}

    public function handle(Login $event): void
    {
        auth()->user()->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}
