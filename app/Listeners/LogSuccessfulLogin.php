<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\Menu;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    use Menu;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $rol = auth()->user()->roles->first->get();
        $menu = $this->construirMenu($rol->id);
        session(['menu' => $menu]);
        session(['roles' => auth()->user()->roles]);
        session(['rolActual' => $rol]);
    }
}
