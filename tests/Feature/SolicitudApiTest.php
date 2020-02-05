<?php

namespace Tests\Feature;

use App\Solicitud;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class SolicitudApiTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

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
                        'estatus_solicitud_id',
                        'objeto_solicitud_id',
                        'centro_id',
                        'user_id',
                        'ratificada',
                        'solicita_excepcion',
                        'fecha_ratificacion',
                        'fecha_recepcion',
                        'fecha_conflicto',
                        'observaciones',
                        'deleted_at',
                        'created_at',
                        'updated_at',
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
        'user' => [
            'id','name','email','email_verified_at','deleted_at','created_at','updated_at','persona_id'
        ],
        'estatus_solicitud' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'objeto_solicitud' => [
            'id', 'nombre','deleted_at', 'updated_at', 'created_at'
        ],
        'centro' => [
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
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud');
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
        
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud?page=2');
        $response->assertStatus(200);
        $res = $response->json();
        $this->assertEquals(2, $res['data']['current_page']);
    }

    /**
     * Al solicitar la relación persona debe regresar la estructura con user
     *
     * @test
     * @return void
     */
    public function testSolicitudConUser(): void
    {
        $jsonConUser = $this->jsonPaginado;
        $jsonConUser['data']['data'][0]['user'] = $this->jsonRelaciones['user'];
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud?load=user');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonConUser);
    }

    /**
     * Al solicitar la relación persona debe regresar la estructura con estatus solicitud
     *
     * @test
     * @return void
     */
    public function testSolicitudConEstatusSolicitud(): void
    {
        $jsonCompuesto = $this->jsonPaginado;
        $jsonCompuesto['data']['data'][0]['estatus_solicitud'] = $this->jsonRelaciones['estatus_solicitud'];
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud?load=estatusSolicitud');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonCompuesto);
    }

    /**
     * Al solicitar la relación persona debe regresar la estructura con objeto solicitud
     *
     * @test
     * @return void
     */
    public function testSolicitudConObjetoSolicitud(): void
    {
        $jsonCompuesto = $this->jsonPaginado;
        $jsonCompuesto['data']['data'][0]['objeto_solicitud'] = $this->jsonRelaciones['objeto_solicitud'];
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud?load=objetoSolicitud');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonCompuesto);
    }

    /**
     * Al solicitar la relación persona debe regresar la estructura con centros
     *
     * @test
     * @return void
     */
    public function testSolicitudConCentros(): void
    {
        $jsonCompuesto = $this->jsonPaginado;
        $jsonCompuesto['data']['data'][0]['centro'] = $this->jsonRelaciones['centro'];
        factory(Solicitud::class,20)->create();
        $response = $this->json('GET', '/api/solicitud?load=centro');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonCompuesto);
    }
}
