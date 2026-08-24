<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'Драма', 'Комедия', 'Боевик', 'Триллер', 'Фантастика',
            'Ужасы', 'Мелодрама', 'Анимация', 'Фэнтези', 'Криминал',
        ])->each(fn (string $name) => Genre::firstOrCreate(['name' => $name]));
    }
}
