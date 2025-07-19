<?php

namespace XiDanko\Jasper;

use Illuminate\Support\ServiceProvider;
use XiDanko\TsEnumsGenerator\Console\Commands\Generate;

class JasperServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'jasper');
    }

    public function boot(): void
    {
        $this->publishes([__DIR__ . '/../config/config.php' => config_path('jasper.php')], 'jasper-config');
    }
}
