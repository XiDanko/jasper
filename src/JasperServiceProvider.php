<?php

namespace XiDanko\Jasper;

use Illuminate\Support\ServiceProvider;

class JasperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('jasper', function() {
            return new Jasper();
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'jasper');
    }

    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/config.php' => config_path('jasper.php')], 'jasper-config');
    }
}
