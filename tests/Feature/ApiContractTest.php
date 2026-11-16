<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\ApiFixtureSeeder;
use Tests\TestCase;

/**
 * Tests de caracterizacion: congelan el comportamiento actual de /api/*.
 * Cadena de prueba: ver tests/fixtures/data.json (meta en el mismo archivo).
 */
class ApiContractTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Salvaguarda: nunca correr contra la BD real.
        $this->assertSame('escalebridge_test', config('database.connections.mysql.database'));

        // Seed explicito: $seed/$seeder de RefreshDatabase no existen en Laravel 7.
        $this->seed(ApiFixtureSeeder::class);
    }

    private function snapshot($name)
    {
        $path = __DIR__ . '/../fixtures/snapshots/' . $name . '.json';

        return json_decode(file_get_contents($path), true);
    }

    public function test_regiones_ok()
    {
        $response = $this->getJson('/api/regiones');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => ['*' => ['idRegion', 'nombreRegion', 'pointX', 'pointY', 'zoom', 'created_at', 'updated_at']],
        ]);
        $response->assertExactJson($this->snapshot('regiones'));
    }

    public function test_provincias_ok()
    {
        $response = $this->getJson('/api/provincias/01');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => ['*' => ['idProvincia', 'nombreProvincia', 'pointX', 'pointY', 'created_at', 'updated_at']],
        ]);
        $response->assertExactJson($this->snapshot('provincias'));
    }

    public function test_provincias_id_inexistente_devuelve_404()
    {
        $response = $this->getJson('/api/provincias/99');

        $response->assertStatus(404);
        $response->assertExactJson([
            'status' => 'error',
            'message' => 'No se encontraron provincias para esta región.',
        ]);
    }

    public function test_distritos_ok()
    {
        $response = $this->getJson('/api/distritos/0101');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => ['*' => ['idDistrito', 'nombreDistrito', 'created_at', 'updated_at']],
        ]);
        $response->assertExactJson($this->snapshot('distritos'));
    }

    public function test_distritos_id_inexistente_devuelve_404()
    {
        $response = $this->getJson('/api/distritos/9999');

        $response->assertStatus(404);
        $response->assertExactJson([
            'status' => 'error',
            'message' => 'No se encontraron distritos para esta provincia.',
        ]);
    }

    public function test_colegios_ok()
    {
        $response = $this->getJson('/api/colegios/010104');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => ['*' => [
                'codMod', 'idDistrito', 'nombreCenEdu', 'cenPob', 'dirCen',
                'director', 'dis_crecer', 'dis_juntos', 'created_at', 'updated_at',
            ]],
        ]);
        $response->assertExactJson($this->snapshot('colegios'));
    }

    public function test_colegios_id_inexistente_devuelve_404()
    {
        $response = $this->getJson('/api/colegios/999999');

        $response->assertStatus(404);
        $response->assertExactJson([
            'status' => 'error',
            'message' => 'No se encontraron escuelas para este distrito.',
        ]);
    }
}
