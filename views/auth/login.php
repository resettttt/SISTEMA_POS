<?php
/**
 * Vista de Login
 * Formulario para iniciar sesión con usuario y contraseña
 */
session_start();
require_once '../../utils/helpers.php';

// Si ya está autenticado, redirigir según rol
if (isAuthenticated()) {
    switch ($_SESSION['user_rol_id']) {
        case ROLE_ADMIN:
            header('Location: ../admin/dashboard.php');
            break;
        case ROLE_WORKER:
            header('Location: ../worker/dashboard.php');
            break;
        case ROLE_CLIENT:
            header('Location: ../client/quick_sale.php');
            break;
        default:
            header('Location: ../auth/login.php');
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mt-5">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Iniciar Sesión</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error = getErrorMessage()): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="login">
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small><a href="#">¿Olvidó su contraseña?</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>