# Bridges Note – Migración PHP 7.4 → 8.3

## Stack
- Laravel 7.30.7, API REST, MySQL/MariaDB (BD: escalebridge, utf8mb4_unicode_ci)
- Sincroniza datos de MINEDU/ESCALE: regiones, provincias, distritos, colegios

## Entorno (Windows, portable)
- PHP 7.4: C:\dev\php74\php.exe  (NO usar el php del PATH, es 8.5)
- MariaDB 10.11: C:\dev\mariadb, root sin password
- Arranque: ./start.ps1
- SSL: php.ini necesita curl.cainfo y openssl.cafile → cacert.pem

## Comandos
- Composer: C:\dev\php74\php.exe <composer.phar> install  (composer.phar NO encontrado en C:\dev ni en el proyecto; ruta por confirmar)
- Servir: C:\dev\php74\php.exe artisan serve
- Sync: C:\dev\php74\php.exe artisan sync:locations (lento)
- Tests: C:\dev\php74\php.exe vendor/bin/phpunit

## Endpoints (contrato a preservar)
- GET /api/regiones
- GET /api/provincias/{idRegion}
- GET /api/distritos/{idProvince}
- GET /api/colegios/{ubigeo}

## Plan de migración
Laravel 7 → 8 → 9 → 10, un commit por salto. Objetivo final: PHP 8.3 + Laravel 10.

## Reglas
- Un salto de versión por sesión. Plan primero, luego cambios.
- No commitear .env ni start.ps1.
- Respuestas cortas: solo errores y diffs relevantes, sin logs completos.
- Antes de cada commit: tests en verde.
## Archivos clave
- routes/api.php — 4 rutas GET → LocationController (+ /user con auth:api)
- app/Http/Controllers/LocationController.php — getRegions/getProvinces/getDistricts/getSchools
- app/Http/Controllers/Controller.php — base
- app/Console/Commands/SyncLocations.php — comando sync:locations
- vendor/ presente (dependencias ya instaladas)
