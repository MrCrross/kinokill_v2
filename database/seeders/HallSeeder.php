<?php

namespace Database\Seeders;

use App\Models\Hall;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $halls = [
            ['name' => 'Красный зал', 'rows_count' => 8, 'columns_count' => 12, 'price_standard' => 350, 'price_vip' => 600],
            ['name' => 'Синий зал', 'rows_count' => 6, 'columns_count' => 10, 'price_standard' => 300, 'price_vip' => 500],
            ['name' => 'IMAX', 'rows_count' => 12, 'columns_count' => 16, 'price_standard' => 450, 'price_vip' => 800],
        ];

        foreach ($halls as $attributes) {
            $hall = Hall::create($attributes);
            $hall->generateSeats();
        }
    }
}
