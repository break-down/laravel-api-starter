<?php

declare( strict_types = 1 );

namespace Tests\Feature\Http\Api\Registration;

use App\Mail\Registration\AlreadyExists;
use App\Mail\Registration\Verify;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Http\Response;
use Illuminate\Support\Testing\Fakes\MailFake;
use Tests\TestCase;

final class StoreTest extends TestCase
{
    protected MailFake $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(Mailer::class, $this->mailer = new MailFake());
    }

    public function test()
    {
        $response = $this->json('post', 'registration', [
            'name' => 'Break Down',
            'email' => 'info@break-down.local',
        ]);

        $response->assertOk();

        $this->mailer->assertQueued(Verify::class, function(Verify $verify) {
            return $verify->hasTo('info@break-down.local');
        });

        $this->assertDatabaseHas('users', [
            'name' => 'Break Down',
            'email' => 'info@break-down.local',
        ]);
    }

    public function testEmailAlreadyExists()
    {
        UserFactory::new()->createOne([
            'email' => 'info@break-down.local',
        ]);

        $response = $this->json('post', 'registration', [
            'name' => 'Break Down',
            'email' => 'info@break-down.local',
        ]);

        $response->assertOk();

        $this->mailer->assertQueued(AlreadyExists::class, function(AlreadyExists $verify) {
            return $verify->hasTo('info@break-down.local');
        });
    }

    public function testValidationErrors()
    {
        $response = $this->json('post', 'registration');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonValidationErrors([
            'name', 'email',
        ]);
    }
}
