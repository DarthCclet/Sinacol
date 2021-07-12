<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Redirect default
     */
    const HOME_DEFAULT = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * En ausencia de la variable redirectTo este método redirecciona dinámicamente al usuario después del login exitoso
     * @return string
     */
    public function redirectTo() {
        $home = self::HOME_DEFAULT;
        if (count(Auth::user()->roles) && Auth::user()->roles->first()->home) {
            $home = Auth::user()->roles->first()->home;
        }
        return $home;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), true
        );
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/login');
    }
}
