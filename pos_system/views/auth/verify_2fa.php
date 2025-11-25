<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de 2FA - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mt-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Verificación de Dos Factores</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <p class="text-center">Se ha enviado un código de verificación a su dispositivo.</p>
                        
                        <!-- Mostrar el código para fines de demostración - en producción se enviaría por correo -->
                        <?php if (isset($_SESSION['two_factor_code'])): ?>
                            <div class="alert alert-info text-center">
                                <strong>Código de demostración: <?php echo $_SESSION['two_factor_code']; ?></strong>
                                <br><small>Este código es solo para propósitos de demostración</small>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="?controller=auth&action=verify_2fa">
                            <div class="mb-3">
                                <label for="code" class="form-label">Código de Verificación</label>
                                <input type="text" class="form-control" id="code" name="code" maxlength="6" required>
                                <div class="form-text">Ingrese el código de 6 dígitos</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Verificar</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="?controller=auth&action=login" class="btn btn-link">Volver al login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>