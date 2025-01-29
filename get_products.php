<?php
header('Content-Type: application/json');
require_once 'config.php';

$sucursal = $_GET['sucursal'] ?? '';
if (empty($sucursal)) {
    echo json_encode(['success' => false, 'message' => 'Sucursal no especificada.']);
    exit;
}

try {
    $pdo = connectToDatabase($sucursal);

    $query = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE status = 1");
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
