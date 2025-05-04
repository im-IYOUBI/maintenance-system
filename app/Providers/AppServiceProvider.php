<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Panel;
use Filament\PanelProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the client and worker panels
        $this->app->singleton('filament.client.panel', function () {
            return app(Filament\Panel::class)
                ->id('client')
                ->path('client')
                ->login()
                ->colors([
                    'primary' => [
                        50 => '238, 242, 255',
                        100 => '224, 231, 255',
                        200 => '199, 210, 254',
                        300 => '165, 180, 252',
                        400 => '129, 140, 248',
                        500 => '99, 102, 241',
                        600 => '79, 70, 229',
                        700 => '67, 56, 202',
                        800 => '55, 48, 163',
                        900 => '49, 46, 129',
                        950 => '30, 27, 75',
                    ],
                ])
                ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
                ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
                ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
                ->brandName('Client Portal');
        });

        $this->app->singleton('filament.worker.panel', function () {
            return app(Filament\Panel::class)
                ->id('worker')
                ->path('worker')
                ->login()
                ->colors([
                    'primary' => [
                        50 => '240, 253, 244',
                        100 => '220, 252, 231',
                        200 => '187, 247, 208',
                        300 => '134, 239, 172',
                        400 => '74, 222, 128',
                        500 => '34, 197, 94',
                        600 => '22, 163, 74',
                        700 => '21, 128, 61',
                        800 => '22, 101, 52',
                        900 => '20, 83, 45',
                        950 => '5, 46, 22',
                    ],
                ])
                ->discoverResources(in: app_path('Filament/Worker/Resources'), for: 'App\\Filament\\Worker\\Resources')
                ->discoverPages(in: app_path('Filament/Worker/Pages'), for: 'App\\Filament\\Worker\\Pages')
                ->discoverWidgets(in: app_path('Filament/Worker/Widgets'), for: 'App\\Filament\\Worker\\Widgets')
                ->brandName('Technician Portal');
        });
    }
}
