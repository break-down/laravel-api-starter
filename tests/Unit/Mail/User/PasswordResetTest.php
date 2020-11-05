<?php

declare( strict_types = 1 );

namespace Tests\Unit\Mail\User;

use App\Mail\User\PasswordReset;
use Database\Factories\UserFactory;
use Tests\TestCase;

final class PasswordResetTest extends TestCase
{
    public function test()
    {
        $user = UserFactory::new()->createOne();

        $mailable = ( new PasswordReset($user, 'info@break-down.local') );
        $mailable->render();

        $this->assertArrayHasKey('password_reset_url', $mailable->viewData);
        $this->assertArrayHasKey('user', $mailable->viewData);
        $this->assertStringContainsString('info@break-down.local', $mailable->viewData[ 'password_reset_url' ]);
    }
}
