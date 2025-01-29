<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia si usas otro usuario
$password = ""; // Cambia si tienes una contraseña para tu base de datos
$dbname = "arepas"; // Nombre de tu base de datos global

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Usuario y contraseña
$user = "alejo";
$plainPassword = "alejito";

// Encriptar la contraseña con bcrypt
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Actualizar la contraseña en la base de datos
$sql = "UPDATE users SET password = '$hashedPassword' WHERE username = '$user'";

if ($conn->query($sql) === TRUE) {
    echo "Contraseña actualizada y encriptada correctamente.";
} else {
    echo "Error al actualizar la contraseña: " . $conn->error;
}

$conn->close();
?>
