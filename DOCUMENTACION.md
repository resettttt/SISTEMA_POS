# Documentación del Sistema de Autenticación Avanzado

## Índice
1. [Descripción General](#descripción-general)
2. [Características Principales](#características-principales)
3. [Arquitectura del Sistema](#arquitectura-del-sistema)
4. [Flujo de Autenticación](#flujo-de-autenticación)
5. [Roles de Usuario](#roles-de-usuario)
6. [Seguridad](#seguridad)
7. [Estructura de Directorios](#estructura-de-directorios)

## Descripción General

El Sistema de Autenticación Avanzado es una aplicación web desarrollada en PHP que implementa un sistema de autenticación seguro con doble factor de verificación (2FA) y un sistema de roles bien definido. La aplicación permite gestionar usuarios, clientes y ventas según el rol del usuario autenticado.

## Características Principales

### 1. Autenticación con Doble Factor (2FA)
- **Login con credenciales**: El usuario ingresa email y contraseña
- **Verificación adicional**: Se solicita un código OTP (One-Time Password) de 6 dígitos
- **Expiración de códigos**: Los códigos expiran después de 5 minutos
- **Códigos de un solo uso**: No se pueden reutilizar una vez usados
- **Sistema de envío**: Actualmente simulado, listo para integrar con email/SMS

### 2. Sistema de Roles
- **Administrador**: Acceso total al sistema
- **Trabajador**: Acceso limitado con permisos específicos
- **Cliente**: Acceso a funciones básicas y ventas rápidas

### 3. Gestión de Usuarios
- Creación, edición y eliminación de usuarios
- Asignación de roles a usuarios
- Control de estado (activo/inactivo)

### 4. Gestión de Clientes
- Visualización de información de clientes
- Edición de datos de clientes (solo por trabajadores/admins)
- Registro de clientes ocasionales

### 5. Sistema de Ventas Rápidas
- Registro de ventas sin necesidad de registro previo
- Opción para clientes registrados y ocasionales
- Almacenamiento de información de ventas

## Arquitectura del Sistema

El sistema sigue el patrón MVC (Modelo-Vista-Controlador):

### Modelo
- **User.php**: Gestión básica de usuarios
- **Admin.php**: Funcionalidades específicas para administradores
- **Worker.php**: Funcionalidades específicas para trabajadores
- **Client.php**: Funcionalidades específicas para clientes
- **TwoFactorAuth.php**: Gestión de autenticación de dos factores

### Vista
- **auth/**: Formularios de autenticación (login, 2FA)
- **admin/**: Interfaces para administradores
- **worker/**: Interfaces para trabajadores
- **client/**: Interfaces para clientes

### Controlador
- **AuthController.php**: Gestión del proceso de autenticación
- **AdminController.php**: Lógica de negocio para administradores
- **WorkerController.php**: Lógica de negocio para trabajadores
- **ClientController.php**: Lógica de negocio para clientes

## Flujo de Autenticación

1. **Paso 1: Inicio de sesión**
   - Usuario ingresa email y contraseña
   - Sistema verifica credenciales en la base de datos
   - Si son correctas, se almacena información básica en sesión

2. **Paso 2: Generación de código 2FA**
   - Sistema genera un código OTP de 6 dígitos
   - Se almacena en la tabla `two_factor_auth` con tiempo de expiración
   - Se "envía" el código al usuario (actualmente simulado)

3. **Paso 3: Verificación 2FA**
   - Usuario ingresa el código recibido
   - Sistema verifica que el código sea válido, no expirado y no usado
   - Si es correcto, se marca como usado y se autentica completamente al usuario

4. **Paso 4: Redirección según rol**
   - Administrador → dashboard_admin.php
   - Trabajador → dashboard_trabajador.php
   - Cliente → quick_sale.php

## Roles de Usuario

### Administrador (Rol 1)
- **Acceso**: Total al sistema
- **Permisos**:
  - Crear, editar y eliminar usuarios (administradores, trabajadores, clientes)
  - Gestionar productos
  - Gestionar ventas
  - Acceso a todos los módulos
  - Ver y modificar toda la información

### Trabajador (Rol 2)
- **Acceso**: Limitado al sistema
- **Permisos**:
  - Visualizar toda la información
  - Modificar datos de clientes
  - NO puede eliminar clientes
  - NO puede modificar o eliminar usuarios, productos o ventas
  - NO puede acceder a zonas de administrador
  - Registrar ventas rápidas

### Cliente (Rol 3)
- **Acceso**: Básico al sistema
- **Permisos**:
  - Acceso a venta rápida
  - Puede ser cliente registrado o ocasional
  - No puede acceder a áreas restringidas
  - No puede gestionar otros usuarios

## Seguridad

### Medidas Implementadas
- **Hash de contraseñas**: Uso de `password_hash()` con algoritmo bcrypt
- **Prevención de SQL Injection**: Consultas preparadas en todas las operaciones
- **Sanitización de entradas**: Limpieza y validación de todos los datos de entrada
- **Control de sesiones**: Validación de sesión en todas las páginas protegidas
- **Protección contra XSS**: Uso de `htmlspecialchars()` en la salida de datos
- **Middleware de autenticación**: Verificación de permisos antes de acceso a recursos
- **Códigos de un solo uso**: Los códigos 2FA no se pueden reutilizar

### Validaciones
- Verificación de roles antes de ejecutar funciones
- Validación de datos de entrada en formularios
- Control de acceso a rutas específicas según rol
- Prevención de acceso directo a archivos de controlador

## Estructura de Directorios

```
/
├── config/                 # Archivos de configuración
│   ├── database.php        # Configuración de base de datos
│   └── config.php          # Constantes y configuración general
├── controllers/            # Controladores del sistema
│   ├── AuthController.php  # Controlador de autenticación
│   ├── AdminController.php # Controlador de administrador
│   ├── WorkerController.php # Controlador de trabajador
│   └── ClientController.php # Controlador de cliente
├── models/                 # Modelos del sistema
│   ├── User.php            # Modelo de usuario base
│   ├── Admin.php           # Modelo de administrador
│   ├── Worker.php          # Modelo de trabajador
│   ├── Client.php          # Modelo de cliente
│   └── TwoFactorAuth.php   # Modelo de 2FA
├── views/                  # Vistas del sistema
│   ├── auth/               # Vistas de autenticación
│   │   ├── login.php       # Formulario de login
│   │   └── verify_2fa.php  # Formulario de verificación 2FA
│   ├── admin/              # Vistas de administrador
│   │   ├── dashboard.php   # Dashboard de admin
│   │   └── users.php       # Gestión de usuarios
│   ├── worker/             # Vistas de trabajador
│   │   ├── dashboard.php   # Dashboard de trabajador
│   │   └── clients.php     # Gestión de clientes
│   └── client/             # Vistas de cliente
│       └── quick_sale.php  # Venta rápida
├── middleware/             # Middleware de autenticación
│   └── AuthMiddleware.php  # Middleware de control de acceso
├── utils/                  # Funciones de utilidad
│   └── helpers.php         # Funciones auxiliares
├── public/                 # Archivo principal
│   └── index.php           # Router principal
├── assets/                 # Recursos estáticos
│   ├── css/                # Hojas de estilo
│   └── js/                 # Scripts JavaScript
└── README.md               # Documentación general
```

## Consideraciones Finales

Este sistema proporciona una base sólida para una aplicación con requisitos de seguridad moderados a altos. La arquitectura modular permite fácil extensión y mantenimiento. La implementación de 2FA añade una capa adicional de seguridad crítica para aplicaciones que manejan información sensible.

Para producción, se recomienda:
- Implementar un sistema real de envío de códigos (email/SMS)
- Añadir logging de actividades
- Implementar políticas de contraseña más estrictas
- Añadir protección contra ataques de fuerza bruta
- Configurar HTTPS para todas las comunicaciones