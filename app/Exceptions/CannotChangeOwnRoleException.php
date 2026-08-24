<?php

namespace App\Exceptions;

use RuntimeException;

class CannotChangeOwnRoleException extends RuntimeException
{
    public static function create(): self
    {
        return new self('Нельзя снять с себя роль администратора.');
    }
}
