<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Enums\Icons\PhosphorIcons;
use App\Enums\UserRole;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Settings\ContactChannelsSettingsPage;
use App\Filament\Pages\Settings\GeneralSettingsPage;
use App\Filament\Pages\Settings\RewardSettingsPage;
use App\Filament\Resources\Users\UserResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('hub')
            ->login()
            ->registration(Register::class)
            ->profile(isSimple: false)
            ->colors([
                'primary' => Color::Blue,
                'success' => Color::hex('#238273'),
                'danger' => Color::Rose,
                'warning' => Color::hex('#ffb347'),
            ])
            ->resourceEditPageRedirect('index')
            ->resourceCreatePageRedirect('index')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('Ibm Plex Sans Arabic')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('2.2rem')
            ->favicon(asset('favicon.png'))
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                GeneralSettingsPage::class,
                ContactChannelsSettingsPage::class,
                RewardSettingsPage::class,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Shipments Management'))
                    ->icon(PhosphorIcons::PackageDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Users Management'))
                    ->icon(PhosphorIcons::UsersDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Companies Management'))
                    ->icon(PhosphorIcons::BuildingOfficeDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Financial Operations'))
                    ->icon(PhosphorIcons::VaultDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Countries Management'))
                    ->icon(PhosphorIcons::GlobeStandDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Contact and Help Center')),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Content Management'))
                    ->icon(PhosphorIcons::FilesDuotone),

                NavigationGroup::make()
                    ->collapsible()
                    ->label(fn () => __('Settings'))
                    ->icon(PhosphorIcons::GearDuotone),
            ])
            ->navigationItems([
                NavigationItem::make('new_shipment')
                    ->label(fn (): string => __('New Shipment'))
//                    ->url(fn(): string => ShipmentResource::getUrl('create'))
                    ->sort(1)
                    ->group(fn (): string => __('Shipments Management')),

                // User Management Child Items
                NavigationItem::make('all')
                    ->label(fn (): string => __('All accounts'))
                    ->isActiveWhen(fn (): bool => 'all' === request()->get('tab'))
                    ->visible(auth()->check() && auth()->user()->hasRole(UserRole::ADMIN))
                    ->url(fn (): string => UserResource::getUrl('index') . '?tab=all')
                    ->group(fn (): string => __('Users Management')),

                NavigationItem::make('admins')
                    ->label(fn (): string => __('Administration'))
                    ->isActiveWhen(fn (): bool => 'admins' === request()->get('tab'))
                    ->visible(auth()->check() && auth()->user()->hasRole(UserRole::ADMIN))
                    ->url(fn (): string => UserResource::getUrl('index') . '?tab=admins')
                    ->group(fn (): string => __('Users Management')),

                NavigationItem::make('customers')
                    ->label(fn (): string => __('Customers'))
                    ->isActiveWhen(fn (): bool => 'users' === request()->get('tab'))
                    ->visible(auth()->check() && auth()->user()->hasRole(UserRole::ADMIN))
                    ->url(fn (): string => UserResource::getUrl('index') . '?tab=users')
                    ->group(fn (): string => __('Users Management')),
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
