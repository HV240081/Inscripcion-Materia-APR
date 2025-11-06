<?php
// Ruta al archivo de conexión
include_once("../config/conexion.php");

// Validar datos de entrada
if (!isset($_POST['id_tutoria']) || !isset($_POST['id_estudiante'])) {
    echo json_encode(["status" => "error", "msg" => "Datos incompletos."]);
    exit;
}

$id_tutoria = intval($_POST['id_tutoria']);
$id_estudiante = intval($_POST['id_estudiante']);

// 1️⃣ Verificar si la tutoría existe y obtener datos
$sql = "SELECT cupo_maximo FROM tutorias WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tutoria);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "msg" => "Tutoría no encontrada."]);
    exit;
}

$tutoria = $result->fetch_assoc();
$maximo = (int)$tutoria['cupo_maximo'];

// 2️⃣ Verificar si el estudiante ya está inscrito
$sqlCheck = "SELECT id FROM inscripciones WHERE id_tutoria = ? AND id_alumno = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $id_tutoria, $id_estudiante);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["status" => "warning", "msg" => "Ya estás inscrito en esta tutoría."]);
    exit;
}

// 3️⃣ Verificar cuántos están inscritos actualmente
$sqlCount = "SELECT COUNT(*) AS total FROM inscripciones WHERE id_tutoria = ?";
$stmt = $conn->prepare($sqlCount);
$stmt->bind_param("i", $id_tutoria);
$stmt->execute();
$resCount = $stmt->get_result()->fetch_assoc();

if ($resCount['total'] >= $maximo) {
    echo json_encode(["status" => "full", "msg" => "El cupo de esta tutoría está lleno."]);
    exit;
}

// 4️⃣ Si hay espacio, insertar inscripción
$sqlInsert = "INSERT INTO inscripciones (id_tutoria, id_alumno, estado, fecha_inscripcion)
              VALUES (?, ?, 'pendiente', NOW())";
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param("ii", $id_tutoria, $id_estudiante);

if ($stmt->execute()) {
    echo json_encode(["status" => "ok", "msg" => "Inscripción realizada correctamente."]);
} else {
    echo json_encode(["status" => "error", "msg" => "Error al guardar la inscripción."]);
}
?>
