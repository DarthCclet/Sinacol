<?php

namespace App\Providers;

use App\Services\ContadorServiceFederal;
use App\Services\ContadorService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ContadorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ContadorService::class, Config::get('folios.proveedor_contador_service'));
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
