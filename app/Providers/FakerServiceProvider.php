<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('FakerCurp',function($app){
            $faker = \Faker\Factory::create();
            $newClass = new class($faker) extends \Faker\Provider\Base {
                public function curp()
                {
                    // $curp = "AAGP910507HPLBRR02";
                    $curp = $this->lexify('????');
                    $curp .= $this->numberBetween(70,99);
                    $curp .= $this->numberBetween(10,12);
                    $curp .= $this->numberBetween(10,30);
                    $curp .= $this->lexify('??????');
                    $curp .= $this->numberBetween(1,9);
                    return strtoupper($curp);
                }
                public function rfc()
                {
                    $curp = $this->lexify('????');
                    $curp .= $this->numberBetween(70,99);
                    $curp .= $this->numberBetween(10,12);
                    $curp .= $this->numberBetween(10,30);
                    $curp .= $this->lexify('??');
                    $curp .= $this->numberBetween(1,9);
                    return strtoupper($curp);
                }
            };
            $faker->addProvider($newClass);
            return $faker;
        });
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
