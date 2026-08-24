<?php

namespace App\Data\Session;

use App\Data\Film\FilmListItemData;
use Spatie\LaravelData\Data;

class FilmScheduleData extends Data
{
    /**
     * @param  array<int, SessionListItemData>  $sessions
     */
    public function __construct(
        public FilmListItemData $film,
        public array $sessions,
    ) {}
}
