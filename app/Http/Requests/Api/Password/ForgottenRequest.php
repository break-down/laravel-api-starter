<?php

declare( strict_types = 1 );

namespace App\Http\Requests\Api\Password;

use Illuminate\Foundation\Http\FormRequest;

final class ForgottenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [ 'required', 'string', 'email:rfc,dns', ],
        ];
    }
}
