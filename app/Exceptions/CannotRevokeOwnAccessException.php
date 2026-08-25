<?php

namespace App\Exceptions;

use RuntimeException;

class CannotRevokeOwnAccessException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Нельзя назначить себе роль, которая лишит вас права управлять ролями пользователей.');
    }
}
