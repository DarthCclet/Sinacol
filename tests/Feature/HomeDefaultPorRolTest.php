<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\LoginController;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HomeDefaultPorRolTest extends TestCase
{
    // Para no guardar datos en la BD después del test
    use DatabaseTransactions;

    /**
     * @test
     *
     * @return void
     */
    public function debe_mostrar_home_default_de_usuario_sin_rol()
    {
        # Este usuario no tiene rol asignado.
        $user = factory(User::class)->create([
            'password' => $password = 'test.test',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);

        # Por lo tanto después de loguearse debe ser redireccionado al home default
        $response->assertRedirect(LoginController::HOME_DEFAULT);
    }

    /**
     * @test
     *
     * @return void
     */
    public function debe_mostrar_home_de_usuario_con_rol_con_home_asignado()
    {
        $user = factory(User::class)->create([
            'password' => $password = 'test.test',
        ]);

        # Creamos un rol con un URL como home
        Role::create([
            'name' => $nombre_rol = 'Rol test con home',
            'descripcion' => 'Rol con ruta de home asignado',
            'home' => $ruta_test = '/home-test-x234'
        ]);

        # A este usuario se le asigna un rol con un home
        $user->assignRole($nombre_rol);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);

        # Después de autenticarse el usuario debe redireccionarse a la ruta del rol
        $response->assertRedirect($ruta_test);
    }

    /**
     * @test
     */
    public function debe_mostrar_home_default_de_usuario_con_rol_sin_home_asignado()
    {
        $user = factory(User::class)->create([
            'password' => $password = 'test.test',
        ]);

        # Creamos un rol sin home.
        Role::create([
            'name' => $nombre_rol_sin_home = 'Rol test sin ruta asignada',
            'descripcion' => 'Rol de prueba sin ruta asignada'
        ]);

        # Asignamos el rol sin home al usuario
        $user->assignRole($nombre_rol_sin_home);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);

        # Después de loguearse el usuario debe ser redirigido al home default
        $response->assertRedirect(LoginController::HOME_DEFAULT);
    }
}
