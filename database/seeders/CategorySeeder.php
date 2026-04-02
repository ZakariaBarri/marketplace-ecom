<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catgList = [
            'T-Shirts',
            'Chemises',
            'Pantalons',
            'Costumes',
            'Vetements de sport',
            'Robes',
            'Tops',
            'Jupes'
        ];

        foreach ($catgList as $catg) {
            Category::create(['name' => $catg]);
        }
       
    }
}
