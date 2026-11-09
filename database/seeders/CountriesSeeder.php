<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'Австралия', 'Бельгия', 'Великобритания', 'Германия', 'Гонконг',
            'Дания', 'Индия', 'Ирландия', 'Испания', 'Италия', 'Канада', 'Китай',
            'Нидерланды', 'Новая Зеландия', 'Норвегия', 'Польша', 'Россия',
            'США', 'Турция', 'Украина', 'Финляндия', 'Франция', 'Чехия',
            'Швейцария', 'Швеция', 'Южная Корея', 'Япония',
        ])->each(fn (string $name) => Country::firstOrCreate(['name' => $name]));
    }
}
