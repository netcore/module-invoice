<?php

namespace Modules\Invoice\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Invoice\Repositories\InvoiceRepository;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Barryvdh\Snappy\ServiceProvider::class);
        $this->registerAliases();
    }

    /**
     * Register aliases.
     */
    public function registerAliases()
    {
        AliasLoader::getInstance()->alias('PDF', \Barryvdh\Snappy\Facades\SnappyPdf::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('netcore/module-invoice.php'),
            __DIR__ . '/../Config/snappy.php' => config_path('netcore/module-invoice-snappy.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'netcore.module-invoice'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/invoice');
        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath,
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/invoice';
        }, config('view.paths')), [$sourcePath]), 'invoice');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/invoice');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'invoice');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'invoice');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
