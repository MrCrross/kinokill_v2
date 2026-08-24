<?php

namespace App\Exceptions;

use RuntimeException;

class SessionOverlapException extends RuntimeException
{
    public static function create(): self
    {
        return new self('В этом зале уже есть сеанс, пересекающийся по времени (с учётом уборки зала).');
    }
}
