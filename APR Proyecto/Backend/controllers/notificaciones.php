<?php
// Mostrar errores solo en entorno de desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once("../config/conexion.php");

// Verificar que el usuario tenga sesión activa
$id_usuario = $_SESSION["id_usuario"] ?? null;
if (!$id_usuario) {
    http_response_code(401);
    echo json_encode(["error" => "No hay sesión activa"]);
    exit;
}

// Consulta para obtener las notificaciones del usuario actual
$sql = "SELECT mensaje, fecha, leido FROM notificaciones WHERE id_usuario = ? ORDER BY fecha DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Error al preparar la consulta SQL: " . $conn->error]);
    exit;
}

$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$notificaciones = [];
while ($row = $result->fetch_assoc()) {
    $notificaciones[] = $row;
}

echo json_encode($notificaciones);
?>
