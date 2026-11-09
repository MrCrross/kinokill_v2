<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class StoreUserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $roleId,
    ) {}
}
