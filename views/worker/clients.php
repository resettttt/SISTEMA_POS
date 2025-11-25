<?php
/**
 * Vista de Gestión de Clientes
 * Lista todos los clientes para trabajadores
 */
require_once '../../middleware/AuthMiddleware.php';
AuthMiddleware::checkRole(ROLE_WORKER);

require_once '../../utils/helpers.php';
require_once '../../controllers/WorkerController.php';

$controller = new WorkerController();
$clients = $controller->worker->getAllClients();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes - <?php echo APP_NAME; ?></title>
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
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="clients.php" class="list-group-item list-group-item-action active">Gestionar Clientes</a>
                    <a href="../client/quick_sale.php" class="list-group-item list-group-item-action">Venta Rápida</a>
                    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Gestionar Productos (Restringido)</a>
                    <a href="#" class="list-group-item list-group-item-action disabled" tabindex="-1" aria-disabled="true">Gestionar Ventas (Restringido)</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Clientes</h2>
                </div>
                
                <?php if ($success = getSuccessMessage()): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if ($error = getErrorMessage()): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Estado</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clients) > 0): ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td><?php echo $client['id']; ?></td>
                                        <td><?php echo htmlspecialchars($client['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                                        <td>
                                            <?php if ($client['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $client['creado_en']; ?></td>
                                        <td>
                                            <a href="edit_client.php?id=<?php echo $client['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">Editar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No hay clientes registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>