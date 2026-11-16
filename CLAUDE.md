# Bridges Note – Laravel 13 / PHP 8.3

## Stack
- Laravel 13, PHP 8.3, API REST, MySQL/MariaDB (BD: escalebridge, utf8mb4_unicode_ci)
- Sincroniza datos de MINEDU/ESCALE: regiones, provincias, distritos, colegios
- Migrado desde Laravel 7 / PHP 7.4 (ver "Historial de migración")

## Entorno (Windows, portable)
- PHP 8.3: C:\dev\php83\php.exe  (NO usar el php del PATH, es 8.5)
- PHP 7.4 (solo histórico): C:\dev\php74\php.exe
- MariaDB 10.11: C:\dev\mariadb, root sin password
- Arranque: ./start.ps1 (no versionado; sirve `php -S 127.0.0.1:8000 -t public`)
- SSL: php.ini necesita curl.cainfo y openssl.cafile → C:\dev\php74\cacert.pem

## Comandos
- Composer: C:\dev\php83\php.exe C:\php\composer.phar <comando>
- Servir: C:\dev\php83\php.exe artisan serve
- Sync: C:\dev\php83\php.exe artisan sync:locations (lento; llama a la API de MINEDU)
- Tests: C:\dev\php83\php.exe vendor/bin/phpunit  (PHPUnit 12; BD escalebridge_test)
- Seguridad: composer audit. NO usar --no-security-blocking ni audit.ignore sin autorización expresa.

## Endpoints (contrato a preservar)
- GET /api/regiones
- GET /api/provincias/{idRegion}   (404 si no hay resultados)
- GET /api/distritos/{idProvince}  (404 si no hay resultados)
- GET /api/colegios/{ubigeo}       (404 si no hay resultados)
Respuesta: {"status":"success","data":[...]}; error: {"status":"error","message":"..."}.
Congelado por tests/Feature/ApiContractTest.php + tests/fixtures/snapshots/*.json (NO modificar snapshots sin aprobación).

## Reglas
- Plan primero, luego cambios.
- No commitear .env ni start.ps1.
- Tests SIEMPRE contra escalebridge_test (phpunit.xml lo fuerza); nunca contra escalebridge.
- No correr `migrate` sobre escalebridge sin pedirlo; las migraciones llevan guard hasTable.
- Respuestas cortas: solo errores y diffs relevantes, sin logs completos.
- Antes de cada commit: tests en verde y `artisan about` sin errores.
- Git: identidad local MiguelAndy; no force push sin autorización.

## Archivos clave
- bootstrap/app.php — routing (web + api: routes/api.php), sin Kernel ni middleware propios
- routes/api.php — 4 rutas GET → LocationController
- app/Http/Controllers/LocationController.php — getRegions/getProvinces/getDistricts/getSchools
- app/Console/Commands/SyncLocations.php — comando sync:locations (autodescubierto)
- app/Models/{Region,Province,District,School}.php — modelos con PK string
- database/migrations/ — users (guard hasTable) + 4 tablas de datos
- tests/Support/ApiFixtureSeeder.php + tests/fixtures/ — cadena de prueba (1 región, 7 provincias, 1 distrito, 7 colegios)

## Historial de migración
- Laravel 7 → 8 descartado: Composer bloquea todas las versiones estables 8.x por avisos de seguridad; L8–L11 sin soporte.
- Estrategia final: esqueleto Laravel 13 (PHP 8.3, soporte de seguridad hasta 2028) + portar el código.
- Commits en migracion/php83: esqueleto L13 → modelos/migraciones → rutas/controladores → sync:locations → tests en verde → limpieza/docs.
- Decisiones: sin Sanctum ni ruta /user (boilerplate L7 fuera del contrato); CACHE_STORE=file, QUEUE_CONNECTION=sync, SESSION_DRIVER=array; migraciones cache/jobs del esqueleto eliminadas.
