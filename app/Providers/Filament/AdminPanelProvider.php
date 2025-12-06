<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\FontProviders\GoogleFontProvider;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->maxContentWidth(1280)
            ->colors([
                // Brand / primary (used for active tab, main actions)
                'primary'   => Color::Blue[900],   // warm yellow, not too strong
                'secondary' => Color::Amber[400],    // slightly deeper yellow/amber

                // Semantic colors
                'success'   => Color::Lime[500],     // positive, fresh
                'warning'   => Color::Amber[300],    // light, warm warning
                'danger'    => Color::Rose[300],     // light red (not orange)
                'info'      => Color::Yellow[500],   // more intense yellow for info highlights

                // Neutrals
                'gray'  => Color::Zinc[600],
                'dark'  => Color::Zinc[800],
                'light' => Color::Zinc[50],
            ])
            ->font(
                'Tajawal',
                provider: GoogleFontProvider::class,
            )
            ->sidebarWidth('250px')
            ->sidebarCollapsibleOnDesktop()
            ->plugin(FilamentLanguageSwitcherPlugin::make())
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([

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
