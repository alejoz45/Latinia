<?php
require_once "config.php";

// Permitir acceso desde cualquier origen
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Configuración de la respuesta en JSON
header('Content-Type: application/json');

// Obtener los datos enviados
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$currentPassword = $data['currentPassword'];
$newPassword = $data['newPassword'];

// Consulta para verificar la contraseña actual
session_start();
$username = $_SESSION['username'] ?? ''; // Asegúrate de que el usuario esté autenticado
if (!$username) {
    echo json_encode(["success" => false, "message" => "No has iniciado sesión."]);
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($currentPassword, $user['password'])) {
    // Actualizar la contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE users SET password = :newPassword WHERE username = :username");
    $updateStmt->execute(['newPassword' => $hashedPassword, 'username' => $username]);

    echo json_encode(["success" => true, "message" => "Contraseña actualizada exitosamente."]);
} else {
    echo json_encode(["success" => false, "message" => "La contraseña actual es incorrecta."]);
}
?>
