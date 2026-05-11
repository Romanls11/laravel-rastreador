# MisHábitos — Rastreador de Hábitos Diarios

Aplicación web para registrar y mantener hábitos diarios, con seguimiento de rachas consecutivas y panel de progreso.

## Funcionalidades

- **Panel de control** — vista general con total de hábitos activos, completados hoy y mejor racha activa, más barra de progreso diario
- **Agregar hábitos** — formulario con nombre, descripción, icono (16 opciones) y color (12 opciones) con previsualización en vivo
- **Marcar como completado** — botón de toggle por hábito sin recargar la página
- **Rachas consecutivas** — racha actual (días seguidos hasta hoy) y racha máxima histórica
- **Calendario de 30 días** — vista por hábito con cada día clickeable para registrar o eliminar completados
- **Tasa de cumplimiento** — porcentaje de días completados en los últimos 30 días

## Tecnologías

| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8.1 + Laravel 10 |
| Base de datos | SQLite |
| Vistas | Blade Templates |
| Estilos | Tailwind CSS (CDN) |
| Frontend | Vanilla JavaScript (Fetch API) |

## Requisitos

- PHP 8.1 o superior
- Composer
- Extensiones PHP: `pdo_sqlite`, `sqlite3`

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/Romanls11/laravel-rastreador.git
cd laravel-rastreador

# 2. Instalar dependencias
composer install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 4. Crear la base de datos SQLite
touch database/database.sqlite

# 5. Ejecutar migraciones
php artisan migrate

# 6. Iniciar el servidor
php artisan serve
```

Abre http://localhost:8000 en tu navegador.

> **Windows:** si `touch` no funciona, crea el archivo manualmente o con `New-Item database/database.sqlite`.

## Estructura del proyecto

```
app/
  Http/Controllers/HabitController.php   # CRUD + toggle de completados
  Models/Habit.php                        # Lógica de rachas y estadísticas
  Models/HabitCompletion.php              # Registro diario de completados

database/migrations/
  ..._create_habits_table.php
  ..._create_habit_completions_table.php

resources/views/
  layouts/app.blade.php                   # Layout principal
  habits/index.blade.php                  # Panel de control
  habits/create.blade.php                 # Formulario de creación
  habits/edit.blade.php                   # Formulario de edición
  habits/show.blade.php                   # Detalle + calendario 30 días

routes/web.php                            # Rutas de la aplicación
```

## Rutas principales

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/` | Redirige al panel |
| GET | `/habits` | Panel de control |
| GET | `/habits/create` | Formulario nuevo hábito |
| POST | `/habits` | Guardar nuevo hábito |
| GET | `/habits/{id}` | Detalle del hábito |
| GET | `/habits/{id}/edit` | Formulario edición |
| PUT | `/habits/{id}` | Actualizar hábito |
| DELETE | `/habits/{id}` | Eliminar hábito |
| POST | `/habits/{id}/toggle` | Marcar/desmarcar completado (JSON) |

## Licencia

MIT
