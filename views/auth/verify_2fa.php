<?php
/**
 * Vista de Verificación de 2FA
 * Formulario para ingresar el código de autenticación de dos factores
 */
session_start();
require_once '../../utils/helpers.php';

// Verificar que se necesite 2FA
if (!isset($_SESSION['needs_2fa']) || !$_SESSION['needs_2fa']) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mt-5">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Verificación de Dos Factores</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error = getErrorMessage()): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <p class="text-center">Hemos enviado un código de verificación a su correo electrónico.</p>
                        
                        <form action="../../controllers/AuthController.php" method="POST">
                            <input type="hidden" name="action" value="verify_2fa">
                            
                            <div class="mb-3">
                                <label for="otp" class="form-label">Código de Verificación:</label>
                                <input type="text" class="form-control text-center" id="otp" name="otp" 
                                       maxlength="<?php echo OTP_LENGTH; ?>" required 
                                       placeholder="Ingrese el código de 6 dígitos">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Verificar Código</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <small>¿No recibió el código? <a href="login.php">Reintentar</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enfocar automáticamente el campo de código
        document.getElementById('otp').focus();
    </script>
</body>
</html>