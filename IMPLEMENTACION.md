# Implementación del Sistema de Autenticación Avanzado

## Requisitos del Sistema

- Servidor web (Apache, Nginx o servidor integrado de PHP)
- PHP 7.4 o superior
- MySQL o MariaDB
- Composer (opcional, para manejo de dependencias)

## Instalación Paso a Paso

### 1. Configuración del Servidor

1. Coloque todos los archivos en el directorio raíz de su servidor web
2. Asegúrese de que el directorio tenga permisos adecuados

### 2. Configuración de la Base de Datos

1. Edite el archivo `config/database.php` y actualice las credenciales:
   ```php
   private $host = 'localhost';
   private $db_name = 'sistema_autenticacion';
   private $username = 'su_usuario';
   private $password = 'su_contraseña';
   ```

2. Ejecute el script de inicialización:
   ```bash
   php init_database.php
   ```
   
   Este script creará las tablas necesarias y usuarios de ejemplo:
   - Administrador: admin@ejemplo.com / admin123
   - Trabajador: worker@ejemplo.com / worker123
   - Cliente: client@ejemplo.com / client123

### 3. Configuración del Sistema

Revise y actualice las constantes en `config/config.php` según sus necesidades:
- `BASE_URL`: URL base de su aplicación
- `OTP_LENGTH`: Longitud del código de 2FA
- `OTP_EXPIRATION`: Tiempo de expiración del código (en segundos)

### 4. Configuración del Servidor Web

#### Para Apache:
Asegúrese de tener el archivo `.htaccess` con reglas de reescritura si desea URLs amigables.

#### Para PHP Built-in Server:
```bash
cd /workspace/public
php -S localhost:8000
```

## Características del Sistema

### 1. Autenticación con 2FA
- Inicio de sesión con email y contraseña
- Verificación por código OTP de 6 dígitos
- Código expira en 5 minutos
- No se pueden reutilizar códigos

### 2. Sistema de Roles

#### Administrador (Rol 1)
- Acceso total al sistema
- Gestionar usuarios (crear, editar, eliminar)
- Gestionar productos y ventas
- Acceso a todos los módulos

#### Trabajador (Rol 2)
- Acceso al dashboard de trabajador
- Ver y modificar información de clientes
- Registrar ventas rápidas
- Restricción en la gestión de usuarios, productos y ventas

#### Cliente (Rol 3)
- Acceso a venta rápida
- Puede ser cliente registrado o ocasional
- No requiere registro para compras puntuales

### 3. Seguridad Implementada

- Contraseñas hasheadas con `password_hash()`
- Prevención de inyección SQL con consultas preparadas
- Validación y sanitización de entradas
- Control de sesiones seguro
- Middleware de autenticación y autorización

### 4. Arquitectura MVC

#### Modelo
- `User.php`: Gestión de usuarios básicos
- `Admin.php`: Funcionalidades para administradores
- `Worker.php`: Funcionalidades para trabajadores
- `Client.php`: Funcionalidades para clientes
- `TwoFactorAuth.php`: Gestión de autenticación de 2 factores

#### Vista
- `views/auth/`: Formularios de autenticación
- `views/admin/`: Páginas para administradores
- `views/worker/`: Páginas para trabajadores
- `views/client/`: Páginas para clientes

#### Controlador
- `AuthController.php`: Gestión de login/logout y 2FA
- `AdminController.php`: Lógica para administradores
- `WorkerController.php`: Lógica para trabajadores
- `ClientController.php`: Lógica para clientes

## Personalización

### Agregar Funcionalidades
1. Para añadir nuevas funcionalidades, siga el patrón MVC
2. Cree modelos en `/models/`
3. Cree vistas en los directorios correspondientes en `/views/`
4. Cree controladores en `/controllers/`
5. Añada rutas en `/public/index.php`

### Cambiar el Sistema de 2FA
Actualmente, el sistema imprime el código OTP en la consola. Para implementar un sistema real:

1. Modifique el método `sendOTP()` en `models/TwoFactorAuth.php`
2. Integre un servicio de email (como PHPMailer) o SMS
3. Ejemplo con PHPMailer:
   ```php
   public function sendOTP($email, $otp) {
       // Configurar PHPMailer
       $mail = new PHPMailer(true);
       // ... configuración del correo
       $mail->Body = "Su código de verificación es: $otp";
       return $mail->send();
   }
   ```

## Endpoints Importantes

- `/views/auth/login.php` - Página de inicio de sesión
- `/views/auth/verify_2fa.php` - Verificación de 2FA
- `/views/admin/dashboard.php` - Dashboard de administrador
- `/views/worker/dashboard.php` - Dashboard de trabajador
- `/views/client/quick_sale.php` - Venta rápida

## Solución de Problemas

### Error de Conexión a la Base de Datos
- Verifique las credenciales en `config/database.php`
- Asegúrese de que el servidor de base de datos esté corriendo

### Problemas de Sesión
- Verifique que PHP tenga permisos de escritura en el directorio de sesiones
- Asegúrese de que `session_start()` se llama antes de cualquier salida

### Acceso Denegado
- Verifique que esté autenticado antes de acceder a páginas protegidas
- Confirme que tiene los permisos adecuados para la acción solicitada