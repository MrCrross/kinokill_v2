<?php

namespace App\Exceptions;

use RuntimeException;

class HasUpcomingSessionsException extends RuntimeException
{
    public static function forFilm(): self
    {
        return new self('Нельзя удалить фильм, для которого запланированы будущие сеансы.');
    }

    public static function forHall(): self
    {
        return new self('Нельзя удалить зал, для которого запланированы будущие сеансы.');
    }
}
