<?php
require_once 'config.php';

// Cabeceras de CORS
header("Access-Control-Allow-Origin: http://localhost:3000"); // Permite solicitudes desde este origen
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Cabeceras permitidas
header("Access-Control-Allow-Credentials: true"); // Permitir cookies si es necesario

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Responde con 200 para solicitudes OPTIONS
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    // Validar datos
    if (!isset($data['order_id'], $data['products']) || empty($data['products'])) {
        throw new Exception("Faltan datos requeridos para actualizar la orden.");
    }

    $orderId = $data['order_id'];
    $products = $data['products'];

    $pdo = getDatabaseConnection();
    $pdo->beginTransaction();

    // Actualizar productos en `order_items`
    $stmtUpdate = $pdo->prepare(
        "UPDATE order_items 
         SET quantity = ?, pricee = ?, customer = ?, obs = ? 
         WHERE order_id = ? AND product_id = ?"
    );

    foreach ($products as $product) {
        if (!isset($product['id'], $product['quantity'], $product['pricee'])) {
            throw new Exception("Datos incompletos en uno de los productos.");
        }

        $stmtUpdate->execute([
            $product['quantity'],
            $product['pricee'],
            $product['customer'],
            $product['obs'],
            $orderId,
            $product['id'],
        ]);
    }

    $pdo->commit();

    echo json_encode(["success" => true, "message" => "Orden actualizada correctamente."]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // Código de error interno del servidor
    echo json_encode(["error" => $e->getMessage()]);
}
?>
