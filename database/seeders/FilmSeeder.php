<?php

namespace Database\Seeders;

use App\Models\Film;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class FilmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genreIds = Genre::pluck('id');

        $films = [
            ['title' => 'Дюна: Часть вторая', 'producer' => 'Дени Вильнёв', 'country' => 'США', 'duration_minutes' => 166, 'age_limit' => 16, 'rating' => 8.5],
            ['title' => 'Оппенгеймер', 'producer' => 'Кристофер Нолан', 'country' => 'США', 'duration_minutes' => 180, 'age_limit' => 16, 'rating' => 8.9],
            ['title' => 'Бесславные ублюдки', 'producer' => 'Квентин Тарантино', 'country' => 'США', 'duration_minutes' => 153, 'age_limit' => 18, 'rating' => 8.4],
            ['title' => 'Интерстеллар', 'producer' => 'Кристофер Нолан', 'country' => 'США/Великобритания', 'duration_minutes' => 169, 'age_limit' => 12, 'rating' => 8.7],
            ['title' => 'Зелёная миля', 'producer' => 'Фрэнк Дарабонт', 'country' => 'США', 'duration_minutes' => 189, 'age_limit' => 16, 'rating' => 9.1],
            ['title' => 'Волк с Уолл-стрит', 'producer' => 'Мартин Скорсезе', 'country' => 'США', 'duration_minutes' => 180, 'age_limit' => 18, 'rating' => 8.3],
            ['title' => 'Джентльмены', 'producer' => 'Гай Ричи', 'country' => 'Великобритания', 'duration_minutes' => 113, 'age_limit' => 18, 'rating' => 8.0],
            ['title' => 'Начало', 'producer' => 'Кристофер Нолан', 'country' => 'США/Великобритания', 'duration_minutes' => 148, 'age_limit' => 12, 'rating' => 8.6],
            ['title' => 'Ла-Ла Ленд', 'producer' => 'Дэмьен Шазелл', 'country' => 'США', 'duration_minutes' => 128, 'age_limit' => 12, 'rating' => 8.0],
            ['title' => 'Головоломка 2', 'producer' => 'Келси Манн', 'country' => 'США', 'duration_minutes' => 96, 'age_limit' => 6, 'rating' => 7.8],
            ['title' => 'Тайна Коко', 'producer' => 'Ли Анкрич', 'country' => 'США', 'duration_minutes' => 105, 'age_limit' => 6, 'rating' => 8.4],
            ['title' => 'Барби', 'producer' => 'Грета Гервиг', 'country' => 'США', 'duration_minutes' => 114, 'age_limit' => 16, 'rating' => 7.2],
        ];

        foreach ($films as $attributes) {
            $film = Film::create([
                ...$attributes,
                'description' => "«{$attributes['title']}» — один из значимых фильмов последних лет, собравший высокие оценки критиков и зрителей.",
            ]);

            $film->genres()->sync($genreIds->random(random_int(1, 3))->all());
        }
    }
}
