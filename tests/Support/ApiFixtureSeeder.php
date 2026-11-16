<?php

namespace Tests\Support;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Carga tests/fixtures/data.json (1 region, sus provincias, 1 distrito y sus colegios).
 * Solo para la BD de test; no llama a ninguna API externa.
 */
class ApiFixtureSeeder extends Seeder
{
    public function run()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../fixtures/data.json'), true);

        foreach (['regions', 'provinces', 'districts', 'schools'] as $table) {
            DB::table($table)->insert($data[$table]);
        }
    }
}
