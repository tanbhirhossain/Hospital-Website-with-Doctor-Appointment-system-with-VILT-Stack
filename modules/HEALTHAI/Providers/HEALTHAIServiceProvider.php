<?php

namespace Modules\HEALTHAI\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\HEALTHAI\Services\Agent\OllamaClient;

class HEALTHAIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge module config so values are available via config('healthai.*')
        $this->mergeConfigFrom(__DIR__ . '/../config/healthai.php', 'healthai');

        // Singleton so the HTTP client is reused across the request lifecycle
        $this->app->singleton(OllamaClient::class, function () {
            return new OllamaClient(
                baseUrl:        config('healthai.ollama_url',     'http://192.168.10.200:11434'),
                model:          config('healthai.ollama_model',   'gemma4:31b-cloud'),
                timeoutSeconds: config('healthai.ollama_timeout', 60),
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'healthai');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        if (file_exists(__DIR__ . '/../Routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Publish config so the host app can override values
        $this->publishes([
            __DIR__ . '/../config/healthai.php' => config_path('healthai.php'),
        ], 'healthai-config');
    }
}
