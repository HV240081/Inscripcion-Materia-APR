<?php
session_start();
require_once("../config/conexion.php");

// Verificar sesión de docente
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "docente") {
    header("Location: ../../Frontend/HTML/login.html");
    exit();
}

// Recibir datos del formulario
$id_docente   = $_SESSION["id_usuario"];
$id_materia   = $_POST["materia"];
$titulo       = $_POST["titulo"];
$descripcion  = $_POST["descripcion"];
$lugar        = $_POST["lugar"];
$tipo         = $_POST["tipo"];
$fecha        = $_POST["fecha"];
$hora_inicio  = $_POST["horaInicio"];
$hora_fin     = $_POST["horaFin"];
$cupo_maximo  = (int)$_POST["cupo"];

// Verificar que la fecha no sea anterior a hoy
$hoy = date('Y-m-d');
if ($fecha < $hoy) {
    echo "<script>alert('La fecha no puede ser anterior al día actual.'); window.history.back();</script>";
    exit();
}

// Validaciones básicas
if (empty($id_materia) || empty($titulo) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {
    echo "<script>alert('Por favor complete todos los campos requeridos.'); window.history.back();</script>";
    exit();
}

if ($cupo_maximo < 1 || $cupo_maximo > 20) {
    echo "<script>alert('El cupo máximo debe estar entre 1 y 20 estudiantes.'); window.history.back();</script>";
    exit();
}

// Insertar en la base de datos
$sql = "INSERT INTO tutorias (id_docente, id_materia, titulo, descripcion, lugar, tipo, fecha, hora_inicio, hora_fin, cupo_maximo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssssssi", $id_docente, $id_materia, $titulo, $descripcion, $lugar, $tipo, $fecha, $hora_inicio, $hora_fin, $cupo_maximo);

if ($stmt->execute()) {
    echo "<script>alert('Cupo creado correctamente ✅'); window.location='../../Frontend/HTML/Docente/Index.php';</script>";
} else {
    echo "<script>alert('Error al crear el cupo.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
