<?php

namespace Tests\Feature;

use App\Parte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParteApiTest extends TestCase
{
      /**
     * Estructura esperada paginada
     * @var array
     */
    private $jsonPaginado = [
        'success',
        'data' =>
            [
                'current_page',
                'data' => [
                    [
                        'id', 
                        'solicitud_id', 
                        'tipo_parte_id', 
                        'genero_id', 
                        'tipo_persona_id', 
                        'nacionalidad_id',
                        'entidad_nacimiento_id', 
                        'nombre','primer_apellido',
                        'segundo_apellido',
                        'nombre_comercial',
                        'fecha_nacimiento',
                        'grupo_prioritario_id',
                        'edad',
                        'rfc',
                        'curp', 
                        'deleted_at', 
                        'updated_at', 
                        'created_at'
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ],
        'message'
    ];

     /**
     * Estructura esperada de user
     * @var array
     */
    private $jsonRelaciones = [
        'solicitud' => [
            'id',
            'estatus_solicitud_id','centro_id','user_id','ratificada','fecha_ratificacion','fecha_recepcion','fecha_conflicto',
            'observaciones','deleted_at','created_at','updated_at',
        ],
        'tipo_parte' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'genero' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'tipo_persona' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'nacionalidad' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'entidad_nacimiento' => [
            'id', 'nombre', 'updated_at', 'created_at'
        ],
        'grupo_prioritario' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ]
    ];
   
   /**
     * Una consulta default debe regresar los registros paginados
     *
     * @test
     * @return void
     */
    public function testRegistrosPaginadosTest(): void
    {
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte');
        $response->assertStatus(200);

        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonPaginado);

        //Al crear cien registros debe conener por lo menos 10 páginas la respuesta
        $res = $response->json();
        $this->assertGreaterThanOrEqual(2, $res['data']['last_page']);
    }

     /**
     * Al solicitar la siguiente página debe avanzar el contador de página en respuesta
     *
     * @test
     * @return void
     */
    public function testSiguientePagina(): void
    {
        
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?page=2');
        $response->assertStatus(200);
        $res = $response->json();
        $this->assertEquals(2, $res['data']['current_page']);
    }


       /**
     * Una consulta default debe regresar los registros paginados
     *
     * @test
     * @return void
     */
    public function testParteOneTest(): void
    {
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte/1');
        $response->assertStatus(200);

        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonPaginado["data"]["data"][0]);
    }
     /**
     * Al solicitar la relación persona debe regresar la estructura con solicitud
     *
     * @test
     * @return void
     */
    public function testParteConSolicitud(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['solicitud'] = $this->jsonRelaciones['solicitud'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?load=solicitud');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }

     /**
     * Al solicitar la relación persona debe regresar la estructura con tipoParte
     *
     * @test
     * @return void
     */
    public function testParteConTipoParte(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['tipo_parte'] = $this->jsonRelaciones['tipo_parte'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?load=tipoParte');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }

     /**
     * Al solicitar la relación persona debe regresar la estructura con Genero
     *
     * @test
     * @return void
     */
    public function testParteConGenero(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['genero'] = $this->jsonRelaciones['genero'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?tipo_persona_id=1&load=genero');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }
    /**
     * Al solicitar la relación persona debe regresar la estructura con tipo Persona
     *
     * @test
     * @return void
     */
    public function testParteTipoPersona(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['tipo_persona'] = $this->jsonRelaciones['tipo_persona'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?load=tipoPersona');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }
    /**
     * Al solicitar la relación persona debe regresar la estructura con Nacionalidad
     *
     * @test
     * @return void
     */
    public function testParteConNacionalidad(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['nacionalidad'] = $this->jsonRelaciones['nacionalidad'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?load=nacionalidad');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }
    /**
     * Al solicitar la relación persona debe regresar la estructura con Entidad nacimiento
     *
     * @test
     * @return void
     */
    public function testParteConEntidadNacimiento(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['entidad_nacimiento'] = $this->jsonRelaciones['entidad_nacimiento'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?load=entidadNacimiento');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }
    /**
     * Al solicitar la relación persona debe regresar la estructura con grupo prioritario
     *
     * @test
     * @return void
     */
    public function testParteConGrupoPrioritario(): void
    {
        $jsonRelaciones = $this->jsonPaginado;
        $jsonRelaciones['data']['data'][0]['grupo_prioritario'] = $this->jsonRelaciones['grupo_prioritario'];
        factory(Parte::class,20)->create();
        $response = $this->json('GET', '/api/parte?tipo_persona_id=1&load=grupoPrioritario');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonRelaciones);
    }

}
