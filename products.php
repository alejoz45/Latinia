<?php
require_once 'config.php';

header('Content-Type: application/json');

// Obtener la sucursal desde el parámetro GET
$branch = $_GET['branch'] ?? die(json_encode(["error" => "Sucursal no especificada"]));
file_put_contents("logs/debug.log", "Branch recibido: $branch\n", FILE_APPEND);

// Conectar a la base de datos correspondiente
$pdo = getDatabaseConnection($branch);

try {
    // Filtrar por sucursal y estado activo (status = 1)
    $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE status = 1");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Log para depuración
    file_put_contents("logs/debug.log", "Productos cargados para $branch: " . print_r($products, true) . "\n", FILE_APPEND);

    echo json_encode($products);
} catch (Exception $e) {
    file_put_contents("logs/debug.log", "Error al cargar productos para $branch: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
