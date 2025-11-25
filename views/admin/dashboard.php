<?php
/**
 * Dashboard de Administrador
 * Página principal para usuarios con rol de administrador
 */
require_once '../../middleware/AuthMiddleware.php';
AuthMiddleware::checkRole(ROLE_ADMIN);

require_once '../../utils/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><?php echo APP_NAME; ?></a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Bienvenido, <?php echo $_SESSION['user_nombre']; ?> (Admin)</span>
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
                    <a href="users.php" class="list-group-item list-group-item-action">Gestionar Usuarios</a>
                    <a href="#" class="list-group-item list-group-item-action">Gestionar Productos</a>
                    <a href="#" class="list-group-item list-group-item-action">Gestionar Ventas</a>
                    <a href="#" class="list-group-item list-group-item-action">Reportes</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Panel de Administración</h2>
                    <a href="users.php" class="btn btn-primary">Ver Usuarios</a>
                </div>
                
                <?php if ($success = getSuccessMessage()): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error = getErrorMessage()): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Estadísticas -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_users ?? 0; ?></h5>
                                <p class="card-text">Total Usuarios</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_admins ?? 0; ?></h5>
                                <p class="card-text">Administradores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_workers ?? 0; ?></h5>
                                <p class="card-text">Trabajadores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_clients ?? 0; ?></h5>
                                <p class="card-text">Clientes</p>
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
                        <p>Esta sección mostraría las últimas actividades en el sistema.</p>
                        <ul>
                            <li>Usuario creado: Juan Pérez</li>
                            <li>Venta registrada: $1,250.00</li>
                            <li>Producto actualizado: Laptops</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>