<?php

namespace Tests\Feature;

use App\Solicitud;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserApiTest extends TestCase
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
                        'name',
                        'email',
                        'email_verified_at',
                        'deleted_at',
                        'created_at',
                        'updated_at',
                        'persona_id'
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
     * Estructura esperada de persona
     * @var array
     */
    private $jsonPersona = [
        'persona' => [
            'id', 'nombre', 'primer_apellido', 'segundo_apellido', 'razon_social', 'curp',
            'rfc', 'fecha_nacimiento', 'tipo_persona_id', 'deleted_at', 'updated_at', 'created_at'
        ]
    ];

    /**
     * Estructura esperada de una solicitud
     * @var array
     */
    private $jsonSolicitudes = [
        'solicitudes' => [
            [
                'id', 'abogado_id', 'estatus_solicitud_id', 'motivo_solicitud_id',
                'centro_id', 'user_id', 'ratificada', 'fecha_ratificacion', 'fecha_recepcion',
                'observaciones', 'presenta_abogado', 'deleted_at', 'created_at', 'fecha_conflicto'
            ]
        ]
    ];

    /**
     * Una consulta default debe regresar los registros paginados
     *
     * @test
     * @return void
     */
    public function registrosPaginados(): void
    {
        factory(User::class, 100)->create();

        $response = $this->json('GET', '/api/user');
        $response->assertStatus(200);

        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonPaginado);

        //Al crear cien registros debe conener por lo menos 10 páginas la respuesta
        $res = $response->json();
        $this->assertGreaterThanOrEqual(10, $res['data']['last_page']);
    }

    /**
     * Al solicitar la siguiente página debe avanzar el contador de página en respuesta
     *
     * @test
     * @return void
     */
    public function siguientePagina(): void
    {
        factory(User::class, 100)->create();
        $response = $this->json('GET', '/api/user?page=2');
        $response->assertStatus(200);
        $res = $response->json();
        $this->assertEquals(2, $res['data']['current_page']);
    }

    /**
     * Al solicitar la relación persona debe regresar la estructura con persona
     *
     * @test
     * @return void
     */
    public function usuarioConPersona(): void
    {
        $jsonConPersona = $this->jsonPaginado;
        $jsonConPersona['data']['data'][0]['persona'] = $this->jsonPersona['persona'];

        factory(User::class, 100)->create();

        $response = $this->json('GET', '/api/user?load=persona');
        $response->assertStatus(200);
        $response->assertJsonStructure($jsonConPersona);
    }

    /**
     * Al solicitar la relación solicitudes debe regresar la estructura con solicitudes
     *
     * @test
     * @return void
     */
    public function usuarioConSolicitudes(): void
    {
        $jsonConSolicitudes = $this->jsonPaginado;
        $jsonConSolicitudes['data']['data'][0]['solicitudes'] = $this->jsonSolicitudes['solicitudes'];

        factory(Solicitud::class, 100)->create();
        $response = $this->json('GET', '/api/user?load=solicitudes');

        $response->assertStatus(200);
        $response->assertJsonStructure($jsonConSolicitudes);
    }
}
