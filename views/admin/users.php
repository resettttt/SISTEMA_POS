<?php
/**
 * Vista de Gestión de Usuarios
 * Lista todos los usuarios del sistema para administradores
 */
require_once '../../middleware/AuthMiddleware.php';
AuthMiddleware::checkRole(ROLE_ADMIN);

require_once '../../utils/helpers.php';
require_once '../../controllers/AdminController.php';

$controller = new AdminController();
$users = $controller->admin->getAllUsers();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - <?php echo APP_NAME; ?></title>
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
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="users.php" class="list-group-item list-group-item-action active">Gestionar Usuarios</a>
                    <a href="#" class="list-group-item list-group-item-action">Gestionar Productos</a>
                    <a href="#" class="list-group-item list-group-item-action">Gestionar Ventas</a>
                    <a href="#" class="list-group-item list-group-item-action">Reportes</a>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Usuarios</h2>
                    <a href="create_user.php" class="btn btn-primary">Crear Usuario</a>
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
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                    switch($user['rol_id']) {
                                                        case ROLE_ADMIN: echo 'bg-danger'; break;
                                                        case ROLE_WORKER: echo 'bg-success'; break;
                                                        case ROLE_CLIENT: echo 'bg-info'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                ?>">
                                                <?php echo htmlspecialchars($user['rol_nombre']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user['activo']): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $user['creado_en']; ?></td>
                                        <td>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">Editar</a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): // No permitir auto-eliminación ?>
                                                <a href="../../controllers/AdminController.php?action=deleteUser&user_id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('¿Está seguro de que desea eliminar este usuario?')">Eliminar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay usuarios registrados</td>
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