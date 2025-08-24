<?php

declare(strict_types=1);

use App\Http\Controllers\Web\PaymentController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::redirect('/', Filament::getPanel('admin')->getUrl());

Route::get('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
