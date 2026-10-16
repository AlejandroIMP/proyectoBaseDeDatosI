### Sistema de Gestion de adopciones mascotas

Proyecto para Bases de Datos 1 
6to Semestre Ingenieria en sistemas


### Stack
#### Backend
- Laravel 13
- SQL Server 2022

#### Frontend
- Laravel Breeze (autenticacion y perfil)
- Livewire 3 + Volt
- Tailwind CSS 4

### Vistas
Cada modelo tiene su CRUD en `resources/views/livewire/{modulo}/index.blade.php` (p. ej. `/pets`, `/adoptions`).
Los modulos, sus rutas y el menu se registran en `app/Support/Modules.php`, y las vistas reutilizan
las reglas de validacion de los controladores de `app/Http/Controllers/Api`.



