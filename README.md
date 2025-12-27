# Sistema de Gestión de Empleados - Nexura Test

Sistema web para la gestión de empleados desarrollado con Laravel 12 y JavaScript vanilla. Permite crear, editar, eliminar y visualizar empleados con sus respectivas áreas y roles.

## Tecnologías Utilizadas

- **Backend:** Laravel 12.44.0
- **Frontend:** JavaScript Vanilla, Tailwind CSS 4.0.7, Font Awesome 6.5.1
- **Base de Datos:** MySQL
- **PHP:** 8.4.16
- **Servidor:** Laravel Herd

## Características

- ✅ CRUD completo de empleados
- ✅ Validaciones en frontend (JavaScript) y backend (Laravel FormRequest)
- ✅ Asignación de múltiples roles a empleados
- ✅ Relaciones con áreas de la empresa
- ✅ Notificaciones toast para feedback de usuario
- ✅ Modal reutilizable para crear y editar
- ✅ Validación de campos con expresiones regulares
- ✅ Transacciones de base de datos con rollback

## Requisitos Previos

- PHP >= 8.4
- Composer >= 2.8
- MySQL >= 8.0
- Laravel Herd (opcional, puede usar XAMPP, MAMP, etc.)

## Instalación y Configuración

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio>
cd nexura-test
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configurar Variables de Entorno

Copiar el archivo `.env.example` a `.env`:

```bash
cp .env.example .env
```

Editar el archivo `.env` y configurar la conexión a MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexura_test
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generar Key de la Aplicación

```bash
php artisan key:generate
```

### 5. Crear Base de Datos

Conectarse a MySQL y crear la base de datos:

```bash
mysql -u root -p
```

Dentro de MySQL:

```sql
CREATE DATABASE nexura_test;
EXIT;
```

### 6. Ejecutar Migraciones

Crear todas las tablas en la base de datos:

```bash
php artisan migrate
```

Esto creará las siguientes tablas:
- `roles` - Roles de los empleados
- `areas` - Áreas de la empresa
- `empleados` - Empleados (con validaciones específicas)
- `empleado_rol` - Relación muchos a muchos entre empleados y roles
- `sessions` - Sesiones de Laravel
- `cache` - Sistema de caché
- `jobs` - Cola de trabajos

### 7. Ejecutar Seeders

Poblar las tablas con datos iniciales:

```bash
php artisan db:seed --class=AreaSeeder
php artisan db:seed --class=RolSeeder
```

**Áreas creadas:**
1. Administración
2. Desarrollo
3. Recursos Humanos
4. Ventas

**Roles creados:**
1. Profesional de proyectos - Desarrollador
2. Gerente estratégico
3. Auxiliar administrativo

### 8. Ejecutar el Servidor

#### Servidor Artisan

```bash
php artisan serve
```

El proyecto estará disponible en:

```
http://127.0.0.1:8000
```

## Estructura de la Base de Datos

### Tabla: empleados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | Identificador único (manual) |
| nombre | VARCHAR(255) | Solo letras y espacios con tildes |
| email | VARCHAR(255) | Email único |
| sexo | CHAR(1) | M (Masculino) o F (Femenino) |
| area_id | INTEGER | FK a tabla areas |
| boletin | INTEGER | 0 (No) o 1 (Sí) |
| descripcion | TEXT | Experiencia del empleado |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

### Tabla: areas

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | Identificador único |
| nombre | VARCHAR(255) | Nombre del área |

### Tabla: roles

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | Identificador único |
| nombre | VARCHAR(255) | Nombre del rol |

### Tabla: empleado_rol

| Campo | Tipo | Descripción |
|-------|------|-------------|
| empleado_id | INTEGER | FK a empleados |
| rol_id | INTEGER | FK a roles |

## Reglas de Validación

### Nombre
- Obligatorio
- Máximo 255 caracteres
- Solo letras (con o sin tilde) y espacios
- No permite números ni caracteres especiales

### Email
- Obligatorio
- Formato válido de email
- Único en la base de datos
- Máximo 255 caracteres

### Sexo
- Obligatorio
- Solo acepta 'M' (Masculino) o 'F' (Femenino)

### Área
- Obligatorio
- Debe existir en la tabla de áreas

### Descripción
- Obligatorio
- Tipo texto largo

### Boletín
- Opcional
- 0 (No recibir) o 1 (Recibir)

### Roles
- Obligatorio
- Mínimo 1 rol seleccionado
- Todos los roles deben existir en la tabla

## API Endpoints

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | /api/empleados | Listar todos los empleados |
| GET | /api/empleados/{id} | Obtener un empleado |
| POST | /api/empleados | Crear empleado |
| PUT | /api/empleados/{id} | Actualizar empleado |
| DELETE | /api/empleados/{id} | Eliminar empleado |
| GET | /api/areas | Listar áreas |
| GET | /api/roles | Listar roles |

## Características Técnicas

### Frontend
- **JavaScript Vanilla**: Sin frameworks, código modular
- **Validaciones en tiempo real**: Al salir de los campos (blur)
- **Toast notifications**: Sistema de notificaciones apilables
- **Modal reutilizable**: Mismo componente para crear y editar
- **Fetch API**: Comunicación asíncrona con el backend

### Backend
- **Form Requests**: Validación centralizada en `StoreEmpleadoRequest`
- **Transacciones DB**: Uso de `DB::beginTransaction()` y rollback
- **IDs manuales**: Sistema de generación de IDs sin auto-increment
- **Eloquent Relationships**: Relaciones `belongsTo` y `hasMany`
- **Query Builder**: Para operaciones específicas en tablas pivot

## Estructura de Archivos

```
nexura-test/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── EmpleadoController.php
│   │   └── Requests/
│   │       └── StoreEmpleadoRequest.php
│   └── Models/
│       ├── Empleado.php
│       └── Area.php
├── database/
│   ├── migrations/
│   │   ├── 2025_12_27_000001_create_roles_table.php
│   │   ├── 2025_12_27_000002_create_areas_table.php
│   │   ├── 2025_12_27_000003_create_empleados_table.php
│   │   └── 2025_12_27_000004_create_empleado_rol_table.php
│   └── seeders/
│       ├── AreaSeeder.php
│       └── RolSeeder.php
├── public/
│   └── js/
│       └── empleados.js
├── resources/
│   └── views/
│       └── employees.blade.php
├── routes/
│   └── web.php
├── .env
└── README.md
```

## Troubleshooting

### Error: "Table doesn't exist"

Asegúrate de haber ejecutado las migraciones:

```bash
php artisan migrate
```

### Error: "Field 'id' doesn't have a default value"

Este error ya está resuelto. El sistema genera IDs manualmente en el controlador.

### Error: Áreas o roles vacíos

Ejecuta los seeders:

```bash
php artisan db:seed --class=AreaSeeder
php artisan db:seed --class=RolSeeder
```

### Error: CSRF token mismatch

Verifica que el meta tag CSRF esté presente en `employees.blade.php`:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## Comandos Útiles

```bash
# Refrescar migraciones (CUIDADO: Borra todos los datos)
php artisan migrate:fresh

# Refrescar migraciones y ejecutar seeders
php artisan migrate:fresh --seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas
php artisan route:list
```

## Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## Autor

Desarrollado para prueba técnica Nexura - 2025