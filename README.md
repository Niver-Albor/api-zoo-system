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
- **Control de Accesos:** Gestión granular de roles y permisos mediante *Policies* y *Middleware* de Laravel.

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

## Licencia

Este proyecto se encuentra bajo la licencia **MIT**.
