<?php

namespace Tests\Feature;

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
        "success",
        "data" =>
            [
                "current_page",
                "data" => [
                    [
                        "id",
                        "name",
                        "email",
                        "email_verified_at",
                        "deleted_at",
                        "created_at",
                        "updated_at",
                        "persona_id"
                    ]
                ],
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "next_page_url",
                "path",
                "per_page",
                "prev_page_url",
                "to",
                "total"
            ],
        "message"
    ];

    /**
     * Estructura esperada cuando se solicita una persona
     * @var array
     */
    private $jsonConPersona = [
        "success",
        "data" =>
            [
                "current_page",
                "data" => [
                    [
                        "id",
                        "name",
                        "email",
                        "email_verified_at",
                        "deleted_at",
                        "created_at",
                        "updated_at",
                        "persona_id",
                        "persona" => [
                            "id", "nombre", "primer_apellido", "segundo_apellido","razon_social",
                            "curp", "rfc", "fecha_nacimiento", "tipo_persona_id", "deleted_at", "updated_at",
                            "created_at"
                        ],
                    ]
                ],
                "first_page_url",
                "from",
                "last_page",
                "last_page_url",
                "next_page_url",
                "path",
                "per_page",
                "prev_page_url",
                "to",
                "total"
            ],
        "message"
    ];

    /**
     * @test
     * @return void
     */
    public function unaConsultaDefaultDebeRegresarRegistrosPaginados(): void
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
     * @test
     * @return void
     */
    public function alSolicitarLaSiguientePaginaDebeAvanzarElContadorDePaginaEnRespuesta(): void
    {
        factory(User::class, 100)->create();
        $response = $this->json('GET', '/api/user', ['page' => 2]);
        $response->assertStatus(200);
        $res = $response->json();
        $this->assertEquals(2, $res['data']['current_page']);
    }

    /**
     * @test
     * @return void
     */
    public function alSolicitarLaRelacionPersonaDebeRegresarLaestructuraConPersona(): void
    {
        factory(User::class, 100)->create();
        $response = $this->json('GET', '/api/user', ['load' => 'persona']);
        $response->assertStatus(200);
        //La estructura debe corresponder a lo esperado
        $response->assertJsonStructure($this->jsonConPersona);

    }
}
