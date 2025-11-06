<?php
header("Content-Type: application/json");
include_once("../config/conexion.php");

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos."]));
}

$materia_id = isset($_GET['materia_id']) ? intval($_GET['materia_id']) : 0;
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : "";

$query = "SELECT 
    t.id, 
    m.nombre AS materia, 
    u.nombre AS docente,
    t.titulo, 
    t.tipo AS modalidad, 
    t.fecha, 
    t.hora_inicio AS hora,
    (t.cupo_maximo - COUNT(i.id)) AS cupos_restantes
FROM tutorias t
INNER JOIN materias m ON m.id = t.id_materia
INNER JOIN usuarios u ON u.id = t.id_docente
LEFT JOIN inscripciones i ON i.id_tutoria = t.id
WHERE 1=1";

if ($materia_id > 0) {
    $query .= " AND t.id_materia = $materia_id";
}
if (!empty($fecha)) {
    $query .= " AND t.fecha = '$fecha'";
}

$query .= "
GROUP BY t.id
HAVING cupos_restantes > 0  -- 👈 solo muestra tutorías con cupos disponibles
ORDER BY t.fecha DESC, t.hora_inicio ASC
";

$result = $conn->query($query);
$tutorias = [];

while ($row = $result->fetch_assoc()) {
    $tutorias[] = $row;
}

echo json_encode($tutorias);
$conn->close();
?>
