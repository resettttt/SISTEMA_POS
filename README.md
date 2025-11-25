# Sistema de Autenticación Avanzado

Este proyecto implementa un sistema de autenticación con doble factor de verificación (2FA) y roles de usuario.

## Características

1. **Autenticación con doble factor (2FA)**
   - Inicio de sesión con usuario y contraseña
   - Segundo factor de verificación (código OTP)
   
2. **Sistema de roles**
   - **Administrador**: Acceso total al sistema
   - **Trabajador**: Acceso limitado, puede ver y modificar clientes
   - **Cliente casual**: Acceso temporal para compras rápidas

3. **Navegación basada en roles**
   - Redirección automática según rol del usuario

## Estructura del proyecto

```
/
├── config/
│   ├── database.php
│   └── config.php
├── controllers/
│   ├── AuthController.php
│   ├── AdminController.php
│   ├── WorkerController.php
│   └── ClientController.php
├── models/
│   ├── User.php
│   ├── Admin.php
│   ├── Worker.php
│   └── Client.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   ├── verify_2fa.php
│   │   └── register.php
│   ├── admin/
│   │   └── dashboard.php
│   ├── worker/
│   │   └── dashboard.php
│   └── client/
│       └── quick_sale.php
├── middleware/
│   ├── AuthMiddleware.php
│   └── RoleMiddleware.php
├── utils/
│   └── helpers.php
├── public/
│   └── index.php
└── assets/
    ├── css/
    └── js/
```