<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistema POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <?php include 'views/layout.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Bienvenido, Administrador</h2>
                <p class="text-muted">Panel de control del sistema POS</p>
            </div>
        </div>
        
        <!-- Métricas generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Productos</h5>
                        <h3><?php echo $totalProducts; ?></h3>
                        <p class="card-text">Total de productos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Clientes</h5>
                        <h3><?php echo $totalCustomers; ?></h3>
                        <p class="card-text">Total de clientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Ventas</h5>
                        <h3><?php echo $totalSales; ?></h3>
                        <p class="card-text">Total de ventas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Usuarios</h5>
                        <h3><?php echo count($this->userModel->getAllUsers()); ?></h3>
                        <p class="card-text">Total de usuarios</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Últimas ventas -->
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3>Últimas Ventas</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recentSales)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Monto</th>
                                            <th>Método de Pago</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentSales as $sale): ?>
                                            <tr>
                                                <td><?php echo $sale['id']; ?></td>
                                                <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Cliente Casual'); ?></td>
                                                <td>$<?php echo number_format($sale['total_amount'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($sale['created_at'])); ?></td>
                                                <td>
                                                    <a href="?controller=sales&action=detail&id=<?php echo $sale['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">Ver</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No hay ventas registradas aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3>Acciones Rápidas</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="?controller=product&action=index" class="btn btn-outline-primary">Gestionar Productos</a>
                            <a href="?controller=customer&action=index" class="btn btn-outline-success">Gestionar Clientes</a>
                            <a href="?controller=sales&action=index" class="btn btn-outline-info">Ver Ventas</a>
                            <a href="?controller=auth&action=users" class="btn btn-outline-warning">Gestionar Usuarios</a>
                            <a href="?controller=sales&action=create" class="btn btn-outline-danger">Nueva Venta</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>