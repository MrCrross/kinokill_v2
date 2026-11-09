<?php

namespace Database\Seeders;

use App\Models\Director;
use Illuminate\Database\Seeder;

class DirectorsSeeder extends Seeder
{
    /**
     * Directors referenced by FilmSeeder. The admin reference page manages
     * the full list; saving a film with a new director name also registers
     * it automatically (see FilmService).
     */
    public function run(): void
    {
        collect([
            'Дени Вильнёв', 'Кристофер Нолан', 'Квентин Тарантино',
            'Фрэнк Дарабонт', 'Мартин Скорсезе', 'Гай Ричи',
            'Дэмьен Шазелл', 'Келси Манн', 'Ли Анкрич', 'Грета Гервиг',
        ])->each(fn (string $name) => Director::firstOrCreate(['name' => $name]));
    }
}
