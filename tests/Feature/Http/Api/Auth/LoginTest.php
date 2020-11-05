<?php

declare( strict_types = 1 );

namespace Tests\Feature\Http\Api\Auth;

use Database\Factories\UserFactory;
use Illuminate\Http\Response;
use Tests\TestCase;
use function bcrypt;

final class LoginTest extends TestCase
{
    public function test()
    {
        $user = UserFactory::new()->createOne([
            'password' => bcrypt('break-down'),
        ]);

        $response = $this->json('post', 'auth/login', [
            'email' => $user->email,
            'password' => 'break-down',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }

    public function testNonExistentEmail()
    {
        $response = $this->json('post', 'auth/login', [
            'email' => 'info@break-down.local',
            'password' => 'break-down',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function testWrongPassword()
    {
        $user = UserFactory::new()->createOne([
            'password' => bcrypt('break-down'),
        ]);

        $response = $this->json('post', 'auth/login', [
            'email' => $user->email,
            'password' => 'pooper',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors([
            'email',
        ]);
    }

    public function testValidationErrors()
    {
        $response = $this->json('post', 'auth/login');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors([
            'email', 'password',
        ]);
    }
}
