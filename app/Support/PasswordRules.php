<?php

namespace App\Support;

use Illuminate\Validation\Rules\Password;

class PasswordRules
{
    public static function defaults(): Password
    {
        return Password::min(8)->mixedCase()->numbers();
    }

    /** @return array<int, mixed> */
    public static function requiredConfirmed(): array
    {
        return ['required', 'string', self::defaults(), 'confirmed'];
    }

    /** @return array<int, mixed> */
    public static function optionalConfirmed(): array
    {
        return ['nullable', 'string', self::defaults(), 'confirmed'];
    }
}
