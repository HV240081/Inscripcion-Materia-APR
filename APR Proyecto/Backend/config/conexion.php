<?php /*
//Configuracion para xampp
$host = "localhost"; 
$user = "root";
$pass = "";
$db = "sistema_tutorias";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
$conexion = new mysqli($host, $user, $pass, $db, $port);


if ($conn->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8");
*/
// Configuración para WAMP

$host = "localhost"; // usa 127.0.0.1 en lugar de localhost
$port = 3306; // cambia si tu WAMP usa 3308 o el que corresponda
$user = "root";
$pass = "";  // o tu contraseña de MySQL si tiene
$db = "sistema_tutorias";

$conn = new mysqli($host, $user, $pass, $db, $port);
$conexion = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("❌ Error de conexión a la base de datos: " . $conn->connect_error);
}

$conn->set_charset("utf8");

?>
