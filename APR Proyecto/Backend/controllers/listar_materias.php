<?php
header("Content-Type: application/json");
include_once("../config/conexion.php");

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

$query = "SELECT id, nombre FROM materias ORDER BY nombre ASC";
$result = $conn->query($query);

$materias = [];
while ($row = $result->fetch_assoc()) {
    $materias[] = $row;
}

echo json_encode($materias);
$conn->close();
?>
