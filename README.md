# API RESTful - Sistema de Gestión Integral de Zoológico

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

API RESTful backend diseñada puramente para servicios backend, desarrollada bajo principios de **Clean Architecture** y un enfoque estricto de **Test-Driven Development (TDD)** con Pest PHP. El sistema administra la gestión integral de un zoológico, priorizando la modularidad, la seguridad y la integridad de los datos.

---

## Características Principales

- **Arquitectura Limpia & SOLID:** Desacoplamiento estricto de la lógica de negocio frente a los frameworks e infraestructura externa.
- **Enfoque TDD:** Cobertura de funcionalidades y flujos críticos mediante pruebas automatizadas con **Pest PHP**.
- **Autenticación Híbrida:** Soporte dual mediante sesiones basadas en cookies (para clientes web) y tokens de acceso seguro con **Laravel Sanctum** (para aplicaciones cliente externas/móviles).
- **Auditoría y Trazabilidad:** Integración de `owen-it/laravel-auditing` sobre un modelo relacional en **MariaDB** para un registro riguroso de cambios.
- **Control de Accesos:** Gestión granular de roles y permisos mediante *Policies* y *Middleware* de Laravel, basado en **Role-Based Access Control (RBAC)**.

---

## Stack Tecnológico

- **Backend:** PHP 8.2+, Laravel 12.x
- **Testing:** Pest PHP (TDD - Test-Driven Development)
- **Auditoría:** Laravel Auditing
- **Base de Datos:** MariaDB / MySQL
- **Control de Versiones y CI/CD:** Git, GitHub Actions

---

## Estructura del Proyecto

El código fuente organiza sus capas desacoplando la lógica de negocio de los controladores y la infraestructura:

```text
app/
├── Business/       # Interfaces, Services Providers, DTOs (Lógica de Negocio pura)
├── Http/
│   ├── Controllers/# Controladores por módulo
│   ├── Middleware/ # Middleware (autenticación y filtrado de Requests)
│   ├── Request/    # FormRequests (Normalización y validación de entradas)
│   └── Resources/  # ApiResources (Estructuración y formateo de respuestas JSON)
├── Models/         # Modelos de Eloquent (Entidades ORM)
└── Policies/       # Políticas de autorización de accesos
```

---

## Requisitos Previos

Asegúrate de contar con lo siguiente instalado en tu entorno local (ej. WSL / Linux):

- **PHP** >= 8.2
- **Composer**
- **MariaDB** / **MySQL**

---

## Instalación y Configuración

Sigue estos pasos para clonar y levantar la API localmente (sin Docker):

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Niver-Albor/api-zoo-system.git
   cd api-zoo-system
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Configurar el archivo de entorno:**
   ```bash
   cp .env.example .env
   ```
   *Configura los parámetros de conexión a tu base de datos (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) en tu archivo `.env`.*

4. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```
   *(O bien `php artisan migrate:fresh --seed` si necesitas restablecer la base de datos desde cero).*

6. **Iniciar el servidor local:**
   ```bash
   php artisan serve
   ```

---

## Suite de Pruebas (Testing)

El proyecto cuenta con una robusta batería de pruebas automatizadas enfocadas en verificar la lógica de negocio, las autorizaciones y la autenticación.

Para ejecutar los tests, puedes utilizar:
```bash
composer test
```
o directamente mediante Pest:
```bash
./vendor/bin/pest
```

---
## Documentación de la API

Todas las rutas (a excepción del inicio de sesión y la obtención del token CSRF) están protegidas bajo el middleware de autenticación `auth:sanctum`, requiriendo un Token de acceso Bearer o una sesión autenticada mediante cookies.

### 1. Autenticación y Seguridad

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/sanctum/csrf-cookie` | Obtiene la cookie `XSRF-TOKEN` requerida para clientes SPA/web | Cookie de sesión |
| `POST` | `/api/login` | Inicia sesión y emite token de acceso o establece sesión | Estructura JSON con token |
| `POST` | `/api/logout` | Cierra la sesión activa y revoca el token | Mensaje de éxito |

### 2. Usuarios (`/api/users`)

*Parámetro de ruta: `{user}` corresponde al **UUID** del usuario.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/users` | Listar usuarios del sistema | `UserResource` |
| `GET` | `/api/users/{user}` | Mostrar detalles de un usuario (incluye *soft deletes*) | `UserResource` |
| `POST` | `/api/users` | Registrar un nuevo usuario | `UserResource` |
| `PUT` | `/api/users/{user}` | Actualizar información del usuario | `UserResource` |
| `DELETE` | `/api/users/{user}` | Eliminar un usuario (Soft Delete) | Mensaje de éxito |

### 3. Animales (`/api/animals`)

*Parámetro de ruta: `{animal}` corresponde al **código único (`code`)** del animal.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/animals` | Listar catálogo de animales | `AnimalResource` |
| `GET` | `/api/animals/{animal}` | Mostrar detalles de un animal por su código | `AnimalResource` |
| `POST` | `/api/animals` | Registrar un nuevo animal | `AnimalResource` |
| `PUT` | `/api/animals/{animal}` | Actualizar información o estado del animal | `AnimalResource` |
| `DELETE` | `/api/animals/{animal}` | Eliminar registro de un animal | Mensaje de éxito |

### 4. Hábitats (`/api/habitats`)

*Parámetro de ruta: `{habitat}` corresponde al **ID numérico** del hábitat.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/habitats` | Listar hábitats registrados | `HabitatResource` |
| `GET` | `/api/habitats/{habitat}` | Mostrar detalles de un hábitat y sus animales asociados | `HabitatResource` |
| `POST` | `/api/habitats` | Registrar un nuevo hábitat | `HabitatResource` |
| `PUT` | `/api/habitats/{habitat}` | Actualizar datos del hábitat | `HabitatResource` |
| `DELETE` | `/api/habitats/{habitat}` | Eliminar un hábitat | Mensaje de éxito |

### 5. Especies (`/api/species`)

*Parámetro de ruta: `{species}` corresponde al **ID numérico** de la especie.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/species` | Listar catálogo de especies | `SpeciesResource` |
| `GET` | `/api/species/{species}` | Mostrar detalles de una especie y sus animales vinculados | `SpeciesResource` |
| `POST` | `/api/species` | Registrar una nueva especie | `SpeciesResource` |
| `PUT` | `/api/species/{species}` | Actualizar información de una especie | `SpeciesResource` |
| `DELETE` | `/api/species/{species}` | Eliminar una especie | Mensaje de éxito |

### 6. Historial Médico (`/api/med_records`)

*Parámetro de ruta: `{medical_record}` corresponde al **UUID** del registro médico.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/med_records` | Listar registros médicos | `MedicalRecordResource` |
| `GET` | `/api/med_records/{medical_record}` | Mostrar un registro médico específico | `MedicalRecordResource` |
| `POST` | `/api/med_records` | Crear un nuevo registro médico | `MedicalRecordResource` |
| `PUT` | `/api/med_records/{medical_record}` | Crear una enmienda/corrección sobre un registro previo | `MedicalRecordResource` |

### 7. Roles y Permisos

*Parámetro de ruta: `{role}` corresponde al **ID numérico** del rol.*

| Método | Ruta | Descripción | Recurso de Respuesta |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/permissions` | Listar todos los permisos disponibles en el sistema | `PermissionResource` |
| `GET` | `/api/roles` | Listar roles y sus permisos asociados | `RoleResource` |
| `GET` | `/api/roles/{role}` | Mostrar detalles de un rol específico | `RoleResource` |
| `POST` | `/api/roles` | Registrar un nuevo rol asignándole permisos | `RoleResource` |
| `PUT` | `/api/roles/{role}` | Actualizar un rol y sus permisos | `RoleResource` |
| `DELETE` | `/api/roles/{role}` | Eliminar un rol | Mensaje de éxito |

---

## Licencia

Este proyecto se encuentra bajo la licencia **MIT**.
