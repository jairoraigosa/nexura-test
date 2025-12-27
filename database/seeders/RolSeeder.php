<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'nombre' => 'Profesional de proyectos - Desarrollador'],
            ['id' => 2, 'nombre' => 'Gerente estratégico'],
            ['id' => 3, 'nombre' => 'Auxiliar administrativo'],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->insert($rol);
        }
    }
}
