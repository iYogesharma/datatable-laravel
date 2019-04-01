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
        $this->publishes([
            __DIR__.'/config/Table.php' => config_path('datatable.php'),
        ]);

        $this->app->singleton('datatable', function () {
            return new Datatable;
        });

        $this->app->singleton('table', function () {
            return new Table;
        });
    }
}
