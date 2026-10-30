# API RESTful - Sistema de Gestión Integral de Zoológico

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

API RESTful backend diseñada bajo principios de **Test-Driven Design (TDD)** y **Clean Architecture**, desarrollada como parte de la refactorización de un sistema de gestión para la administración de un zoológico. El sistema prioriza la modularidad, la seguridad en la autenticación híbrida y la integridad de datos mediante un diseño relacional sólido.

---

## Características Principales

- **Arquitectura Limpia y DDD:** Desacoplamiento estricto entre la lógica de negocio y la infraestructura, aplicando principios **SOLID**.
- **Autenticación Híbrida:** Soporte dual mediante sesiones basadas en cookies (para clientes web) y tokens de acceso seguro con **Laravel Sanctum** (para aplicaciones móviles/externos).
- **Auditoría y Trazabilidad:** Modelado relacional en **MariaDB** con control de integridad referencial y registro de auditoría (*audit logs*).
- **Control de Accesos:** Gestión granular de roles y permisos mediante *Policies* y *Middleware* de Laravel.

---

## Stack Tecnológico

- **Backend:** PHP 8.2+, Laravel 11
- **Testing:** Pest PHP
- **Base de Datos:** MariaDB
- **Contenedorización:** Docker / WSL
- **Control de Versiones:** Git & GitHub

---

## Estructura del Proyecto (Clean Architecture & DDD)

El proyecto organiza su código fuente desacoplando el dominio de los frameworks externos:

```text
app/
├── Business/    # Interfaces, Services Provider, DTOs (Lógica de Negocio pura)
├── Http/
|    ├── Controllers/    # Controladores de Módulos.
|    ├── Middleware/ # Middleware (autenticación y filtrado de Request)
|    ├── Request/    # FormRequest (Normalización y sanitización de los datos del Input)
|    └── Resources/    # ApiResources (Estructuración de la respuesta a la Request)
├── Models/ # Modelos (Entidades del ORM)
└── Policies/ # Politicas (lógica de negocio respecto a la autorización)
