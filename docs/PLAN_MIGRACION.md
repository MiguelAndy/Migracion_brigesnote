# Plan de migración: PHP 7.4 → PHP 8.3

## Objetivo
Llevar **Bridges Note** (API REST que sirve regiones, provincias, distritos y colegios
de MINEDU/ESCALE) de Laravel 7 / PHP 7.4 a **PHP 8.3**, sin cambiar el contrato de los
4 endpoints `/api/*`.

## Plan original
Saltos sucesivos Laravel 7 → 8 → 9 → 10, un commit por salto, con PHP 7.4 al inicio.

## Por qué cambió
- Al intentar el salto a Laravel 8, **Composer bloqueó todas las versiones estables 8.x**
  (`laravel/framework` v8.0.0 – v8.83.29) por avisos de seguridad; sin el bloqueo solo
  quedaba una rama `8.x-dev` inestable. No se usó ningún bypass (`--no-security-blocking`
  ni `audit.ignore`) y el intento se revirtió (`git checkout` + `composer install`).
- Laravel 8 a 11 ya no reciben correcciones de seguridad (incluido Laravel 10), así que
  pasar por ellos habría dejado el proyecto en versiones sin soporte.

## Plan final
**Laravel 7 → Laravel 13 directo** (requiere PHP 8.3; soporte de seguridad hasta 2028):
crear un esqueleto Laravel 13 con PHP 8.3 y **portar** el código (modelos, migraciones,
rutas, controlador y comando `sync:locations`).

Antes de migrar se escribieron **tests de caracterización** que congelan las respuestas
reales de los 4 endpoints (fixtures y snapshots en `tests/fixtures/`), para demostrar que
el comportamiento no cambia.

## Commits (rama `migracion/php83`)
| Hash | Commit |
|---|---|
| `4ffd5a7` | test: tests de caracterización de la API (sobre Laravel 7) |
| `506c6bc` | chore: esqueleto Laravel 13 + PHP 8.3 |
| `f039344` | feat: portar modelos y migraciones |
| `b354c17` | feat: portar rutas API y controladores |
| `42d022a` | feat: portar comando sync:locations |
| `5185372` | test: tests de caracterización en verde sobre L13 |
| `d7e70d0` | chore: limpieza y CLAUDE.md actualizado |

## Validaciones
- **Tests:** 9 tests, 158 assertions, 0 fallos (PHPUnit 12, PHP 8.3). Los snapshots no se modificaron.
- **Migraciones:** `migrate` en BD vacía correcto; sobre un clon del esquema de producción no falla (guards `hasTable`).
- **Sincronización:** `sync:locations` contra la API real de MINEDU, cortado a 60 s: 1 región, 3 provincias, 30 distritos, 547 colegios.
- **Seguridad:** `composer audit` sin avisos.
- README verificado desde un clon limpio (ver historial del repo).

## Decisiones
- **Ruta `/user` eliminada:** era boilerplate de Laravel 7 con `auth:api`, fuera del contrato.
- **Sin Sanctum ni `install:api`:** la API es pública y de solo lectura; `routes/api.php` se habilita con `api:` en `bootstrap/app.php`.
- **Sin sesión, cola ni cache en BD:** `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `CACHE_STORE=file`; se eliminaron las migraciones de cache y jobs.
- **Modelos en `app/Models`** sin cambios de estructura; tests siempre contra `escalebridge_test`.
