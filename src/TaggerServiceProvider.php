<?php
/**
 *  Copyright (c) 2016-2021 Puerto Parrot. Written by Dimitri Mostrey for https// www.puertoparrot.com
 *  Contact me at admin@puertoparrot.com or dmostrey@yahoo.com
 */

namespace Dimimo\Tagger;

use Illuminate\Support\ServiceProvider;

class TaggerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../Config/config.php' => config_path('tagger.php')]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $config = __DIR__ . '/../Config/config.php';
        // merge default config
        $this->mergeConfigFrom($config, 'tagger');
        $app = $this->app;
        $app->singleton('tagger', function ($app) {
            return new Tagger($app['config']['tagger']);
        });
        $app->alias('Tagger', 'Dimimo\Tagger\Tagger');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Tagger::class];
    }
}
