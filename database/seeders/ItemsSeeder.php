<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Laptop'],
            ['name' => 'Smartphone'],
            ['name' => 'Tablet'],
        ];

        foreach ($items as $item) {
            Item::create($item);
        }
    }
}
