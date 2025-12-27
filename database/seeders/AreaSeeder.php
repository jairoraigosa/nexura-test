<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['id' => 1, 'nombre' => 'Administración'],
            ['id' => 2, 'nombre' => 'Desarrollo'],
            ['id' => 3, 'nombre' => 'Recursos Humanos'],
            ['id' => 4, 'nombre' => 'Ventas'],
        ];

        foreach ($areas as $area) {
            DB::table('areas')->insert($area);
        }
    }
}
