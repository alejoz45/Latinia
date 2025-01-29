<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

function getDatabaseConfig($sucursal) {
    $databases = [
        'fabrica' => ['dbname' => 'fabrica', 'user' => 'root', 'password' => ''],
        'triangulo' => ['dbname' => 'triangulo', 'user' => 'root', 'password' => ''],
        'velez' => ['dbname' => 'velez', 'user' => 'root', 'password' => '']
    ];

    return $databases[strtolower($sucursal)] ?? null;
}

function connectToDatabase($sucursal) {
    $dbConfig = getDatabaseConfig($sucursal);
    if (!$dbConfig) {
        die(json_encode(['success' => false, 'message' => 'Sucursal no válida.']));
    }

    try {
        $dsn = "mysql:host=localhost;dbname=" . $dbConfig['dbname'];
        $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die(json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]));
    }
}
