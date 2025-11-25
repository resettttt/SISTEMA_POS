<?php
require_once 'config/database.php';
require_once 'models/UserModel.php';

$database = new Database();
$conn = $database->getConnection();

// Crear las tablas si no existen
$sql = file_get_contents('database_schema.sql');
if ($conn->multi_query($sql)) {
    echo "Base de datos e inicialización completada exitosamente.\n";
} else {
    echo "Error al inicializar la base de datos: " . $conn->error . "\n";
}

// Crear un usuario administrador por defecto si no existe
$userModel = new UserModel();
$adminUser = $userModel->getUserByUsername('admin');

if (!$adminUser) {
    $userModel->createUser('admin', 'admin@example.com', 'admin123', 'admin', 'Admin', 'User');
    echo "Usuario administrador creado:\n";
    echo "  Usuario: admin\n";
    echo "  Contraseña: admin123\n";
} else {
    echo "Usuario administrador ya existe.\n";
}

$conn->close();
?>