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
    if (!isset($data['products']) || empty($data['products'])) {
        throw new Exception("No se proporcionaron productos para la orden.");
    }

    $products = $data['products'];
    $customer = $data['products'][0]['customer'] ?? null;
    $obs = $data['products'][0]['obs'] ?? null;

    $pdo = getDatabaseConnection();
    $pdo->beginTransaction();

    // Inserta una nueva orden
    $stmtOrder = $pdo->prepare("INSERT INTO orders (status) VALUES (0)");
    $stmtOrder->execute();
    $orderId = $pdo->lastInsertId();

    // Inserta productos en la tabla `order_items`
    $stmtItems = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, pricee, customer, obs)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    foreach ($products as $product) {
        if (!isset($product['id'], $product['quantity'], $product['pricee'])) {
            throw new Exception("Datos incompletos en uno de los productos.");
        }

        $stmtItems->execute([
            $orderId,
            $product['id'],
            $product['quantity'],
            $product['pricee'],
            $customer,
            $obs,
        ]);
    }

    $pdo->commit();

    echo json_encode(["success" => true, "order_id" => $orderId]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); // Código de error interno del servidor
    echo json_encode(["error" => $e->getMessage()]);
}
?>
