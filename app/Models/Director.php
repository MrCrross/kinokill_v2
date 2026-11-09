<?php

namespace App\Models;

use Database\Factories\DirectorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Director extends Model
{
    /** @use HasFactory<DirectorFactory> */
    use HasFactory;
}
