<?php

namespace Tests\Feature;

use Laravel\Passport\Client;
use Tests\TestCase;
use Laravel\Passport\Passport;

class OauthTokenClientsTest extends TestCase
{
    public function testServerCreation()
    {
        Passport::actingAsClient(
            factory(Client::class)->create(),
            ['*']
        );
        $response = $this->get('api/parte');
        $response->assertStatus(200);
    }
}

