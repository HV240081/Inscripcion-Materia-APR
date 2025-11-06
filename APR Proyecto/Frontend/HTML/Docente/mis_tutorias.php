<?php
session_start();
include '../../../Backend/config/conexion.php'; // conexión a MySQL

// --- Verificar sesión del docente ---
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'docente') {
  header("Location: ../Login.html");
  exit();
}

$id_docente = $_SESSION['id_usuario'];

// --- Consultar tutorías del docente ---
$sql = "
  SELECT 
    t.id,
    t.titulo,
    t.descripcion,
    t.tipo,
    t.lugar,
    t.plataforma,
    t.fecha,
    t.hora_inicio,
    t.hora_fin,
    t.cupo_maximo,
    m.nombre AS materia_nombre,
    m.codigo AS materia_codigo
  FROM tutorias t
  INNER JOIN materias m ON t.id_materia = m.id
  WHERE t.id_docente = ?
  ORDER BY t.fecha DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_docente);
$stmt->execute();
$resultado = $stmt->get_result();
$tutorias = $resultado->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Tutorías - Docente</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --bg: #f5f7fb;
      --card: #fff;
      --accent: #0f4db6;
      --muted: #6b6b6b;
      --past: #e6e6e6;
    }
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { margin: 0; background: var(--bg); color: #222; }
    .site-header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 12px 20px; background: #fff; border-bottom: 1px solid #eee;
    }
    .brand-logo { height: 34px; }
    .container { max-width: 960px; margin: 30px auto; padding: 0 14px; }
    h1.title { color: var(--accent); text-align: center; font-size: 24px; margin-bottom: 24px; }
    .tutorias-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 16px;
    }
    .tutoria-card {
      background: var(--card); border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      padding: 16px; transition: transform 0.2s, background 0.2s, opacity 0.2s;
    }
    .tutoria-card.activa:hover { transform: translateY(-3px); }
    .tutoria-card.pasada {
      background: var(--past); color: #777; opacity: 0.8;
    }
    .tutoria-card h3 { margin: 0 0 6px; font-size: 18px; color: var(--accent); }
    .tutoria-card.pasada h3 { color: #666; }
    .tutoria-meta { font-size: 14px; color: var(--muted); margin-bottom: 8px; }
    .btn {
      border: none; border-radius: 8px; cursor: pointer; font-weight: 600;
      padding: 8px 12px; transition: background 0.2s;
    }
    .btn-primary { background: var(--accent); color: #fff; }
    .btn-primary:hover { background: #0d3f99; }
    .btn-disabled { background: #ccc; color: #555; cursor: not-allowed; }
    .footer { text-align: center; color: #777; padding: 20px; font-size: 14px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . "/../includes/header.php"); ?>

  <main class="container">
    <h1 class="title">Mis Tutorías</h1>
    <div class="tutorias-grid">
      <?php
      $hoy = date('Y-m-d');

      if (count($tutorias) === 0) {
        echo "<p style='text-align:center;color:#777;'>Aún no has creado tutorías.</p>";
      } else {
        foreach ($tutorias as $t) {
          $es_pasada = ($t['fecha'] < $hoy);
          $clase = $es_pasada ? 'pasada' : 'activa';

          echo "
            <div class='tutoria-card $clase'>
              <h3>{$t['materia_nombre']}</h3>
              <div class='tutoria-meta'>
                <div>📅 " . date('d/m/Y', strtotime($t['fecha'])) . " — 🕒 {$t['hora_inicio']} a {$t['hora_fin']}</div>
                <div>Modalidad: " . ucfirst($t['tipo']) . "</div>
                <div>Cupo máximo: {$t['cupo_maximo']}</div>
              </div>
              <p>" . htmlspecialchars($t['descripcion'] ?? '') . "</p>
              <button class='btn " . ($es_pasada ? "btn-disabled" : "btn-primary") . "'
                " . ($es_pasada ? "disabled" : "onclick=\"location.href='ver_tutoria.php?id={$t['id']}'\"") . ">
                <i class='fas fa-users'></i> Ver inscritos
              </button>
            </div>
          ";
        }
      }
      ?>
    </div>
  </main>

  <?php include(__DIR__ . "/../includes/footer.php"); ?>

  <script>document.getElementById("year").textContent = new Date().getFullYear();</script>
</body>
</html>
