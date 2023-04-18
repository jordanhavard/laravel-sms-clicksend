<?php

namespace JordanHavard\ClickSend;

use function config;
use Illuminate\Support\ServiceProvider;

class ClickSendServiceProvider extends ServiceProvider
{
    private string $name = 'clicksend';

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    public static function basePath(string $path): string
    {
        return __DIR__.'/..'.$path;
    }

    public function register()
    {
        $this->mergeConfigFrom(self::basePath("/config/{$this->name}.php"), $this->name);

        $this->app->singleton(ClickSendApi::class, function () {

            $username = config('clicksend.username');
            $api_key = config('clicksend.api_key');

            return new ClickSendApi($username, $api_key);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // publishing the config
            $this->publishes([
                self::basePath("/config/{$this->name}.php") => config_path("{$this->name}.php"),
            ], "{$this->name}-config");
        }

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ClickSendApi::class];
    }
}
