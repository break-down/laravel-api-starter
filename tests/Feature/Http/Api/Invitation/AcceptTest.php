<?php

declare( strict_types = 1 );

namespace Tests\Feature\Http\Api\Invitation;

use Database\Factories\UserFactory;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Http\Response;
use Tests\TestCase;

final class AcceptTest extends TestCase
{
    public function test()
    {
        $user = UserFactory::new()->createOne();

        $token = $this->app->make(PasswordBrokerManager::class)->broker('user-invitations')->createToken($user);

        $response = $this->json('post', 'invitation/accept', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'break-down',
            'password_confirmation' => 'break-down',
        ]);

        $response->assertOk();
    }

    public function testWithNonExistentToken()
    {
        $user = UserFactory::new()->createOne();

        $response = $this->json('post', 'invitation/accept', [
            'email' => $user->email,
            'token' => 'does-not-exist',
            'password' => 'break-down',
            'password_confirmation' => 'break-down',
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testValidationErrors()
    {
        $response = $this->json('post', 'invitation/accept');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors([
            'token', 'email', 'password',
        ]);
    }
}
