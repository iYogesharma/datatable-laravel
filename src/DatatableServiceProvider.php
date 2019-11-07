<?php

namespace YS\Datatable;

use Illuminate\Support\ServiceProvider;

class DatatableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $dataHtmlTableConfig=__DIR__.'/config/Table.php';
        $dataTableConfig=__DIR__.'/config/Datatable.php';

        // merge config
        $this->mergeConfigFrom($dataHtmlTableConfig, 'table');

        $this->mergeConfigFrom($dataTableConfig, 'datatable');

        $this->publishes([
            __DIR__.'/config/Datatable.php' => config_path('datatable.php'),
            __DIR__.'/config/Table.php' => config_path('table.php'),

        ],'datatable:config');

        $this->app->singleton('datatable', function () {
            return new Datatable;
        });
        
        $this->app->singleton('table', function () {
            return new Table;
        });
    }
}
