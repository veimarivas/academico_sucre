<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoEstudiosSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('universidades')->insert([
            ['nombre' => 'Universidad Mayor Real y Pontificia San Francisco Xavier de Chuquisaca'],
            ['nombre' => 'Universidad Autónoma Gabriel René Moreno'],
            ['nombre' => 'Universidad Mayor de San Andrés'],
            ['nombre' => 'Universidad Mayor de San Simón'],
            ['nombre' => 'Universidad Católica Boliviana'],
            ['nombre' => 'Universidad Privada Domingo Savio'],
            ['nombre' => 'Universidad Privada de Santa Cruz de la Sierra'],
            ['nombre' => 'Universidad Nur'],
            ['nombre' => 'Universidad Salesiana de Bolivia'],
            ['nombre' => 'Otra'],
        ]);

        DB::table('grados_academicos')->insert([
            ['nombre' => 'Técnico Superior'],
            ['nombre' => 'Licenciatura'],
            ['nombre' => 'Diplomado'],
            ['nombre' => 'Especialidad'],
            ['nombre' => 'Maestría'],
            ['nombre' => 'Doctorado'],
            ['nombre' => 'Postdoctorado'],
        ]);

        DB::table('profesiones')->insert([
            ['nombre' => 'Ingeniero de Sistemas'],
            ['nombre' => 'Ingeniero Civil'],
            ['nombre' => 'Ingeniero Industrial'],
            ['nombre' => 'Ingeniero Comercial'],
            ['nombre' => 'Contador Público'],
            ['nombre' => 'Administrador de Empresas'],
            ['nombre' => 'Abogado'],
            ['nombre' => 'Médico'],
            ['nombre' => 'Arquitecto'],
            ['nombre' => 'Licenciado en Educación'],
            ['nombre' => 'Licenciado en Psicología'],
            ['nombre' => 'Licenciado en Comunicación Social'],
            ['nombre' => 'Licenciado en Derecho'],
            ['nombre' => 'Economista'],
            ['nombre' => 'Otra'],
        ]);
    }
}
