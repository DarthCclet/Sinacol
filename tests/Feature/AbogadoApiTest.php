<?php

namespace Tests\Feature;

use App\Abogado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbogadoApiTest extends TestCase
{

     /**
     * Estructura esperada de abogado
     * @var array
     */
    private $jsonRelaciones = [
        [
            'id', 'nombre', 'primer_apellido', 'segundo_apellido', 'cedula_profesional', 'numero_empleado',
            'email', 'profedet', 'deleted_at', 'updated_at', 'created_at'
        ]
    ];

     /**
     * Estructura esperada de abogado
     * @var array
     */
    private $jsonUno = [
        "abogados"=>[
            'id', 'nombre', 'primer_apellido', 'segundo_apellido', 'cedula_profesional', 'numero_empleado',
            'email', 'profedet', 'deleted_at', 'updated_at', 'created_at']
    ];
   
      /**
     * Una consulta default debe regresar los registros 
     *
     * @test
     * @return void
     */
    public function testAbogadoTest(): void
    {
        factory(Abogado::class,20)->create();
        $response = $this->json('GET', '/api/abogado');
        $response->assertStatus(200);

        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonRelaciones);
    }

       /**
     * Una consulta default debe regresar los registros paginados
     *
     * @test
     * @return void
     */
    public function testAbogadoOneTest(): void
    {
        factory(Abogado::class,20)->create();
        $response = $this->json('GET', '/api/abogado/1');
        $response->assertStatus(200);

        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonUno);
    }
}
