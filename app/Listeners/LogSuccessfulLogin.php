<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\Menu;
use OwenIt\Auditing\Models\Audit;
use Carbon\Carbon;
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
        if($rol != null){
            $menu = $this->construirMenu($rol->id);
            session(['menu' => $menu]);
            session(['roles' => auth()->user()->roles]);
            session(['rolActual' => $rol]);
        }else{
            session(['menu' => array()]);
            session(['roles' => array()]);
            session(['rolActual' => array()]);
        }
        $data = [
            'auditable_id' => auth()->user()->id,
            'auditable_type' => "Logged In",
            'event'      => "Logged In",
            'url'        => request()->fullUrl(),
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => auth()->user()->id,
        ];
        session(['persona' => auth()->user()->persona]);
        session(['centro' => auth()->user()->centro]);

        //create audit trail data
        $details = Audit::create($data);
    }
}
