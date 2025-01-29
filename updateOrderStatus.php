<?php
require_once 'config.php';

header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

try {
    if (!isset($_GET['order_id'])) {
        throw new Exception("Falta el parámetro 'order_id'.");
    }

    $orderId = $_GET['order_id'];
    $pdo = getDatabaseConnection('fabrica');

    // Actualizar el estado de la orden
    $stmt = $pdo->prepare("UPDATE orders SET status = 1 WHERE id = ?");
    $stmt->execute([$orderId]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar el estado de la orden: ' . $e->getMessage()]);
}
?>
