<?php

declare(strict_types=1);

namespace Oldas\PwnedPasswords\Exception;

use RuntimeException;

class CompromisedPasswordException extends RuntimeException
{
    public static function create(): self
    {
        $errorMessage = 'This password has appeared in a data breach and your account could easily be compromised if you were to use it. ';
        $advisoryNote = 'This password cannot be used here and, if you use this password elsewhere, you should change it immediately.';
        return new self(
            $errorMessage . $advisoryNote,
        );
    }
}
