<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Forms\Components\Field;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\Column;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class MacrosServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->bootSaudiRiyalMacro();
        $this->bootMyFatoorahMacro();
    }

    public function bootSaudiRiyalMacro(): void
    {
        $saudiRiyalMacro = function (string $position = 'suffix', array $viewParameters = []): static {

            /** @var Field|Column $this */
            $defaults = [
                'size' => 'lg',
                'color' => 'gray-500',
            ];

            $html = new HtmlString(
                view('filament.components.saudi-riyal', array_merge($defaults, $viewParameters))
            );

            match ($position) {
                'prefix' => $this->prefix($html),
                default => $this->suffix($html),
            };

            return $this;

        };

        Field::macro('saudiRiyal', $saudiRiyalMacro);
        Column::macro('saudiRiyal', $saudiRiyalMacro);
        TextEntry::macro('saudiRiyal', $saudiRiyalMacro);
    }

    public function bootMyFatoorahMacro(): void
    {
        /**
         * Macro to configure HTTP client for MyFatoorah API.
         *
         * Retrieves configuration for MyFatoorah from services configuration,
         * checks for the presence of an API key, and sets up the HTTP client
         * with the base URL and necessary headers.
         *
         * @return PendingRequest Configured HTTP client.
         *
         * @throws RuntimeException if the API key is not configured.
         */
        Http::macro('myFatoorah', function () {
            $config = config('services.myfatoorah');

            if (empty($config['api_key'])) {
                throw new RuntimeException('MyFatoorah API key is not configured');
            }

            return Http::baseUrl($config['base_url'])
                ->withHeaders([
                    'Authorization' => "Bearer {$config['api_key']}",
                ]);
        });
    }
}
