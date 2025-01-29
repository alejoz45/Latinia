<?php
header("Content-Type: application/json");

$sucursal = $_GET['sucursal'] ?? null;
if (!$sucursal) {
    echo json_encode([
        "success" => false,
        "message" => "Sucursal no especificada."
    ]);
    exit;
}

$servername = "localhost";
$username = "root"; // Reemplaza con el usuario correcto
$password = ""; // Reemplaza con la contraseña correcta
$dbname = $sucursal; // Usar la base de datos específica de la sucursal

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos: " . $conn->connect_error
    ]);
    exit;
}

// Consulta para obtener las órdenes pendientes (status = 0)
$sql = "SELECT id, total, created_at, payment_method FROM orders WHERE status = 0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            "id" => $row["id"],
            "total" => floatval($row["total"]), // Asegura que sea un número
            "created_at" => $row["created_at"],
            "payment_method" => $row["payment_method"]
        ];
    }

    echo json_encode([
        "success" => true,
        "orders" => $orders
    ]);
} else {
    echo json_encode([
        "success" => true,
        "orders" => []
    ]);
}

$conn->close();
?>
