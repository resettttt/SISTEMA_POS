<?php
/**
 * Controlador de Cliente
 * Maneja las operaciones específicas para clientes ocasionales
 */

require_once '../config/database.php';
require_once '../models/Client.php';
require_once '../utils/helpers.php';

class ClientController {
    
    private $database;
    private $db;
    private $client;

    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
        $this->client = new Client($this->db);
    }

    /**
     * Mostrar página de venta rápida para clientes ocasionales
     */
    public function showQuickSale() {
        // Verificar si el usuario es cliente o si es un cliente ocasional
        if (isset($_SESSION['user_rol_id']) && $_SESSION['user_rol_id'] == ROLE_CLIENT) {
            // Usuario registrado como cliente
            include '../views/client/quick_sale.php';
        } else {
            // Cliente ocasional (sin registro)
            include '../views/client/quick_sale_guest.php';
        }
    }

    /**
     * Procesar venta rápida
     */
    public function processQuickSale() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre_cliente = trim($_POST['nombre_cliente'] ?? '');
            $email_cliente = trim($_POST['email_cliente'] ?? '');
            $monto = floatval($_POST['monto'] ?? 0);
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Validar monto
            if ($monto <= 0) {
                $_SESSION['error'] = 'El monto debe ser mayor a 0.';
                header('Location: ../views/client/quick_sale.php');
                exit();
            }

            // Para clientes ocasionales, no se crea un registro de usuario
            // Solo se registra la venta
            if (empty($nombre_cliente) && empty($email_cliente)) {
                // Cliente ocasional sin datos
                $venta_exitosa = $this->registerSale(null, $monto, $descripcion, true);
            } else {
                // Cliente con datos, verificar si ya existe o crear uno nuevo
                $user_data = $this->client->login($email_cliente, 'temporal'); // Contraseña temporal para login
                if (!$user_data) {
                    // Crear cliente temporal si no existe
                    $this->client->createUser($nombre_cliente, $email_cliente, 'temporal123', ROLE_CLIENT);
                    $user_data = $this->client->login($email_cliente, 'temporal123');
                }
                
                $venta_exitosa = $this->registerSale($user_data['id'], $monto, $descripcion, false);
            }

            if ($venta_exitosa) {
                $_SESSION['success'] = 'Venta registrada exitosamente.';
                header('Location: ../views/client/quick_sale.php');
            } else {
                $_SESSION['error'] = 'Error al registrar la venta.';
                header('Location: ../views/client/quick_sale.php');
            }
            exit();
        } else {
            header('Location: ../views/client/quick_sale.php');
            exit();
        }
    }

    /**
     * Registrar venta en el sistema
     * @param int|null $cliente_id ID del cliente registrado o null para cliente ocasional
     * @param float $monto Monto de la venta
     * @param string $descripcion Descripción de la venta
     * @param bool $es_cliente_ocasional Indica si es un cliente ocasional
     * @return bool
     */
    private function registerSale($cliente_id, $monto, $descripcion, $es_cliente_ocasional) {
        try {
            // En un sistema real, aquí se crearía un registro en una tabla de ventas
            // Por ahora, simplemente simulamos el registro
            
            // Crear tabla de ventas si no existe
            $create_table = "CREATE TABLE IF NOT EXISTS ventas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                cliente_id INT NULL,
                monto DECIMAL(10,2) NOT NULL,
                descripcion TEXT,
                es_cliente_ocasional BOOLEAN DEFAULT FALSE,
                fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (cliente_id) REFERENCES usuarios(id) ON DELETE SET NULL
            )";
            
            $this->db->exec($create_table);
            
            $query = "INSERT INTO ventas (cliente_id, monto, descripcion, es_cliente_ocasional) 
                      VALUES (:cliente_id, :monto, :descripcion, :es_cliente_ocasional)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':es_cliente_ocasional', $es_cliente_ocasional, PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error al registrar venta: " . $e->getMessage());
            return false;
        }
    }
}