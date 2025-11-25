<?php
/**
 * Dashboard de Trabajador
 * Página principal para usuarios con rol de trabajador
 */
require_once '../../middleware/AuthMiddleware.php';
AuthMiddleware::checkRole(ROLE_WORKER);

require_once '../../utils/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Trabajador - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo APP_NAME; ?></a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Bienvenido, <?php echo $_SESSION['user_nombre']; ?> (Trabajador)</span>
                <a class="nav-link" href="../../controllers/AuthController.php?action=logout">Cerrar Sesión</a>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="clients.php" class="list-group-item list-group-item-action">Gestionar Clientes</a>
                    <a href="../client/quick_sale.php" class="list-group-item list-group-item-action">Venta Rápida</a>
                    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Gestionar Productos (Restringido)</a>
                    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Gestionar Ventas (Restringido)</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel de Trabajador</h2>
                    <a href="clients.php" class="btn btn-success">Ver Clientes</a>
                </div>
                
                <?php if ($success = getSuccessMessage()): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error = getErrorMessage()): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Información principal -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>Información</h5>
                            </div>
                            <div class="card-body">
                                <p>Como trabajador, usted tiene los siguientes permisos:</p>
                                <ul>
                                    <li>Visualizar toda la información del sistema</li>
                                    <li>Modificar datos de clientes</li>
                                    <li>Realizar ventas rápidas</li>
                                    <li>Generar reportes limitados</li>
                                </ul>
                                <p class="text-muted">No puede crear, editar o eliminar usuarios, productos o ventas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Acciones Rápidas</h6>
                                <div class="d-grid gap-2">
                                    <a href="clients.php" class="btn btn-outline-success">Gestionar Clientes</a>
                                    <a href="../client/quick_sale.php" class="btn btn-outline-primary">Registrar Venta</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Últimas actividades -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Últimas Actividades</h5>
                    </div>
                    <div class="card-body">
                        <p>Esta sección mostraría las últimas actividades realizadas por el trabajador.</p>
                        <ul>
                            <li>Cliente actualizado: María López</li>
                            <li>Venta registrada: $850.00</li>
                            <li>Cliente agregado: Carlos Ramírez</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>