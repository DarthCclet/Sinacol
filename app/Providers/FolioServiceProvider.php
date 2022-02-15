<?php

namespace App\Providers;

use App\Services\FolioServiceFederal;
use App\Services\FolioService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class FolioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FolioService::class, Config::get('folios.proveedor_folio_service'));
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
