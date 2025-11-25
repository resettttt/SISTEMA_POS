<?php
/**
 * Página de Venta Rápida
 * Para clientes registrados y ocasionales
 */
require_once '../../utils/helpers.php';

// Verificar autenticación opcional (puede ser cliente registrado o invitado)
if (!isAuthenticated() && !isset($_SESSION['user_rol_id'])) {
    // Si no está autenticado, puede acceder como cliente ocasional
} else {
    // Si está autenticado, verificar que sea cliente, trabajador o admin
    if (!isClient() && !isWorker() && !isAdmin()) {
        $_SESSION['error'] = 'Acceso denegado.';
        header('Location: ../auth/login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta Rápida - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo APP_NAME; ?></a>
            <div class="navbar-nav ms-auto">
                <?php if (isAuthenticated()): ?>
                    <span class="navbar-text me-3">Bienvenido, <?php echo $_SESSION['user_nombre']; ?></span>
                    <a class="nav-link" href="../../controllers/AuthController.php?action=logout">Cerrar Sesión</a>
                <?php else: ?>
                    <span class="navbar-text me-3">Cliente Ocasional</span>
                    <a class="nav-link" href="../auth/login.php">Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Registro de Venta Rápida</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success = getSuccessMessage()): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error = getErrorMessage()): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form action="../../controllers/ClientController.php" method="POST">
                            <input type="hidden" name="action" value="processQuickSale">
                            
                            <!-- Datos del cliente (opcional para clientes ocasionales) -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nombre_cliente" class="form-label">Nombre del Cliente:</label>
                                    <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" 
                                           value="<?php echo $_SESSION['user_nombre'] ?? ''; ?>" 
                                           placeholder="Nombre del cliente">
                                    <div class="form-text">Opcional para clientes ocasionales</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email_cliente" class="form-label">Email del Cliente:</label>
                                    <input type="email" class="form-control" id="email_cliente" name="email_cliente" 
                                           value="<?php echo $_SESSION['user_email'] ?? ''; ?>" 
                                           placeholder="Email del cliente">
                                    <div class="form-text">Opcional para clientes ocasionales</div>
                                </div>
                            </div>
                            
                            <!-- Detalles de la venta -->
                            <div class="mb-3">
                                <label for="monto" class="form-label">Monto de la Venta:</label>
                                <input type="number" step="0.01" class="form-control" id="monto" name="monto" 
                                       min="0.01" required placeholder="0.00">
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3" placeholder="Descripción de la venta"></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning">Registrar Venta</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Historial de ventas recientes (solo para usuarios autenticados) -->
                <?php if (isAuthenticated() && (isClient() || isWorker() || isAdmin())): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Ventas Recientes</h5>
                    </div>
                    <div class="card-body">
                        <p>En un sistema real, aquí se mostrarían las ventas recientes.</p>
                        <ul>
                            <li>Venta de $1,200.00 - Producto A</li>
                            <li>Venta de $850.00 - Producto B</li>
                            <li>Venta de $2,500.00 - Producto C</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>