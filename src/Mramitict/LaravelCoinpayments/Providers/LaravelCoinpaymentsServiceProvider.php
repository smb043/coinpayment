<?php namespace Mramitict\LaravelCoinpayments\Providers;

use Illuminate\Support\ServiceProvider;
use Mramitict\LaravelCoinpayments\Facades\Coinpayments;
use Mramitict\LaravelCoinpayments\LaravelCoinpayments;

class LaravelCoinpaymentsServiceProvider extends ServiceProvider {

    const SINGLETON = 'coinpayments';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../../../config/coinpayments.php' => config_path('coinpayments.php')]);

        app()->singleton(self::SINGLETON, function ($app) {
           return new LaravelCoinpayments($app);
        });

        $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');

        class_alias(Coinpayments::class, 'Coinpayments');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
           __DIR__ . '/../../../config/coinpayments.php', 'coinpayments'
        );
    }
}