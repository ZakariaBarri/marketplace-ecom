<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catgList = ['Neuf avec étiquette','Comme neuf','Bon état','Usé'
        ];

        foreach($catgList as $catg){
            Condition::create(['name'=>$catg]);
        }
    }
}
