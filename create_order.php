<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

try {
    // Datos de conexión a la base de datos (ajusta según tu configuración)
    $sucursal = $_GET['sucursal'] ?? '';
    $dbName = '';
    if ($sucursal === 'fabrica') {
        $dbName = 'fabrica';
    } elseif ($sucursal === 'velez') {
        $dbName = 'velez';
    } elseif ($sucursal === 'triangulo') {
        $dbName = 'triangulo';
    } else {
        echo json_encode(['success' => false, 'message' => 'Sucursal inválida.']);
        exit;
    }

    $dsn = "mysql:host=localhost;dbname=$dbName;charset=utf8mb4";
    $db = new PDO($dsn, 'root', ''); // Cambia 'root' y '' según tu configuración de usuario y contraseña
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leer el cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    // Validar los datos recibidos
    if (!isset($input['total'], $input['items']) || !is_array($input['items'])) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos.']);
        exit;
    }

    $customer = $input['customer'] ?? null;
    $obs = $input['obs'] ?? null;
    $total = $input['total'];
    $items = $input['items'];

    // Insertar la orden en la tabla orders
    $stmt = $db->prepare("INSERT INTO orders (customer, obs, total, status, created_at) VALUES (:customer, :obs, :total, 0, NOW())");
    $stmt->execute([
        ':customer' => $customer,
        ':obs' => $obs,
        ':total' => $total
    ]);

    $orderId = $db->lastInsertId();

    // Insertar los detalles de la orden en la tabla order_details
    $stmtDetails = $db->prepare("INSERT INTO order_details (order_id, product_id, quantity, price, subtotal) VALUES (:order_id, :product_id, :quantity, :price, :subtotal)");

    foreach ($items as $item) {
        $stmtDetails->execute([
            ':order_id' => $orderId,
            ':product_id' => $item['id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':subtotal' => $item['total']
        ]);

        // Reducir el stock del producto en la tabla products
        $stmtUpdateStock = $db->prepare("UPDATE products SET stock = stock - :quantity WHERE id = :product_id");
        $stmtUpdateStock->execute([
            ':quantity' => $item['quantity'],
            ':product_id' => $item['id']
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Orden creada exitosamente.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>