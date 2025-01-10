<?php

declare(strict_types=1);

namespace Oldas\PwnedPasswords;

use SensitiveParameter;

interface HaveIBeenPwnedServiceInterface
{
    public function isPwned(
        #[SensitiveParameter]
        string $plaintextPassword,
    ): ?bool;
}
