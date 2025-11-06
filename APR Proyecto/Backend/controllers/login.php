<?php
session_start();
require_once("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carnet = trim($_POST["carnet"]);
    $password = trim($_POST["password"]);

    // Consulta del usuario
    $sql = "SELECT * FROM usuarios WHERE carnet = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $carnet);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Comparar contraseña (por ahora sin hash)
        if ($password === $user["contrasena"]) {
            // Guardar datos en sesión
            $_SESSION["id_usuario"] = $user["id"]; // CORREGIDO
            $_SESSION["nombre"] = $user["nombre"];
            $_SESSION["rol"] = $user["rol"];
            $_SESSION["carnet"] = $user["carnet"];

            // Redirección según rol
            if ($user["rol"] === "docente") {
                header("Location: ../../Frontend/HTML/Docente/Index.php");
            } else {
                header("Location: ../../Frontend/HTML/Estudiante/Index.php");
            }
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
