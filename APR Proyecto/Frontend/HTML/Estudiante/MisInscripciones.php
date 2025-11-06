<?php
session_start();

// Verifica sesión de alumno
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: ../../../index.html");
    exit();
}

require_once("../../../Backend/config/conexion.php");

$mysqli = $conexion ?? $conn ?? null;
if (!$mysqli) {
    die("Error: no hay conexión a la base de datos.");
}

$id_alumno = intval($_SESSION['id_usuario']);
$carnet = $_SESSION['carnet'] ?? '';

$sql = "
SELECT 
  i.id AS id_inscripcion,
  t.titulo,
  t.descripcion,
  t.fecha,
  t.hora_inicio,
  t.hora_fin,
  t.tipo AS modalidad,
  m.nombre AS materia,
  u.nombre AS docente
FROM inscripciones i
INNER JOIN tutorias t ON i.id_tutoria = t.id
INNER JOIN materias m ON t.id_materia = m.id
INNER JOIN usuarios u ON t.id_docente = u.id
WHERE i.id_alumno = ?
ORDER BY t.fecha DESC, t.hora_inicio ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_alumno);
$stmt->execute();
$result = $stmt->get_result();
$inscripciones = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Función para determinar el estado
function obtenerEstadoTutoria($fecha, $horaInicio, $horaFin) {
    $ahora = new DateTime();
    $inicio = new DateTime("$fecha $horaInicio");
    $fin = new DateTime("$fecha $horaFin");

    if ($ahora < $inicio) return ['Pendiente', '#f59e0b'];      // Amarillo
    if ($ahora >= $inicio && $ahora <= $fin) return ['En proceso', '#10b981']; // Verde
    return ['Culminado', '#6b7280'];                            // Gris
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Inscripciones — Estudiante</title>
  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>
  <style>
    body { font-family:'Poppins',sans-serif; margin:0; background:#f5f7fb; color:#222; }
    .page { max-width:1100px; margin:28px auto; padding:16px; }
    header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .brand-logo{height:34px}
    .user-info{background:#eef1f6;padding:8px 12px;border-radius:10px;color:#1e2a78;font-weight:700}
    h1 { color:#0f4db6; margin-bottom:16px; }
    .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:16px; }
    .card {
      background:#fff; border-radius:12px; padding:18px;
      box-shadow:0 4px 12px rgba(0,0,0,0.06);
      display:flex; flex-direction:column; justify-content:space-between;
      transition: transform .2s;
    }
    .card:hover { transform: translateY(-3px); }
    .materia { font-weight:700; color:#1e3a8a; font-size:16px; }
    .tema { font-size:15px; color:#111827; margin:6px 0; }
    .info { font-size:14px; color:#374151; margin:2px 0; }
    .estado {
      display:inline-block; font-weight:700; color:#fff; border-radius:999px;
      padding:5px 10px; font-size:13px; margin-top:8px;
    }
    .btn { background:#0f4db6; color:#fff; border:none; border-radius:8px;
           padding:8px 12px; cursor:pointer; font-weight:600; margin-top:12px;
           transition: background .2s; }
    .btn:hover { background:#173ea7; }
    .empty { text-align:center; padding:30px; color:#666; }
    footer { margin-top:20px; text-align:center; color:#777; font-size:13px; }

    /* Modal */
    .modal {
      display:none; position:fixed; top:0; left:0; width:100%; height:100%;
      background:rgba(0,0,0,0.5); justify-content:center; align-items:center;
      z-index:1000;
    }
    .modal-content {
      background:#fff; padding:22px; border-radius:12px; width:90%; max-width:450px;
      box-shadow:0 5px 15px rgba(0,0,0,0.2); animation:fadeIn .3s ease;
    }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-10px);} to{opacity:1;transform:translateY(0);} }
    .close { float:right; cursor:pointer; color:#555; font-size:18px; font-weight:bold; }
    .close:hover { color:#000; }
    .modal-content h2 { color:#0f4db6; margin-bottom:12px; }
    .modal-content p { margin:4px 0; color:#333; font-size:14px; }
  </style>
</head>
<body>
  <div class="page">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <h1>Mis Inscripciones</h1>

    <?php if (empty($inscripciones)): ?>
      <div class="empty">No estás inscrito en ninguna tutoría por ahora.</div>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($inscripciones as $row): 
          [$estado, $color] = obtenerEstadoTutoria($row['fecha'], $row['hora_inicio'], $row['hora_fin']);
        ?>
        <div class="card">
          <div>
            <div class="materia"><?php echo htmlspecialchars($row['materia']); ?></div>
            <div class="tema"><?php echo htmlspecialchars($row['titulo']); ?></div>
            <div class="info">📅 <?php echo date('d/m/Y', strtotime($row['fecha'])); ?></div>
            <div class="info">🕐 <?php echo substr($row['hora_inicio'],0,5) . " - " . substr($row['hora_fin'],0,5); ?></div>
            <div class="info">🏫 <?php echo htmlspecialchars(ucfirst($row['modalidad'])); ?></div>
            <span class="estado" style="background:<?php echo $color; ?>"><?php echo $estado; ?></span>
          </div>
          <button class="btn"
            onclick="verInfo(
              '<?php echo addslashes($row['materia']); ?>',
              '<?php echo addslashes($row['titulo']); ?>',
              '<?php echo addslashes(date('d/m/Y', strtotime($row['fecha']))); ?>',
              '<?php echo addslashes(substr($row['hora_inicio'],0,5)); ?>',
              '<?php echo addslashes(substr($row['hora_fin'],0,5)); ?>',
              '<?php echo addslashes(ucfirst($row['modalidad'])); ?>',
              '<?php echo addslashes($row['docente']); ?>',
              '<?php echo addslashes($row['descripcion']); ?>'
            )">Ver información</button>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
  </div>

  <!-- Modal -->
  <div class="modal" id="infoModal">
    <div class="modal-content">
      <span class="close" onclick="cerrarModal()">&times;</span>
      <h2>Información de la Tutoría</h2>
      <p id="infoMateria"></p>
      <p id="infoTema"></p>
      <p id="infoFecha"></p>
      <p id="infoHora"></p>
      <p id="infoModalidad"></p>
      <p id="infoDocente"></p>
      <p id="infoDescripcion"></p>
    </div>
  </div>

  <script>
    function verInfo(materia, tema, fecha, horaInicio, horaFin, modalidad, docente, descripcion) {
      document.getElementById('infoMateria').innerText = "Materia: " + materia;
      document.getElementById('infoTema').innerText = "Tema: " + tema;
      document.getElementById('infoFecha').innerText = "Fecha: " + fecha;
      document.getElementById('infoHora').innerText = "Hora: " + horaInicio + " - " + horaFin;
      document.getElementById('infoModalidad').innerText = "Modalidad: " + modalidad;
      document.getElementById('infoDocente').innerText = "Docente: " + docente;
      document.getElementById('infoDescripcion').innerText = "Descripción: " + descripcion;
      document.getElementById('infoModal').style.display = 'flex';
    }

    function cerrarModal() {
      document.getElementById('infoModal').style.display = 'none';
    }

    window.onclick = function(e) {
      const modal = document.getElementById('infoModal');
      if (e.target === modal) cerrarModal();
    }
  </script>
</body>
</html>
