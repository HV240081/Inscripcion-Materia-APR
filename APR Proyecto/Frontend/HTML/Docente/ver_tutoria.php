<?php
include '../../../Backend/config/conexion.php';

// ✅ Obtener el id de la tutoría desde la URL (por ejemplo: ver_tutoria.php?id=3)
$id_tutoria = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si no hay id válido, mostrar error
if ($id_tutoria <= 0) {
  die("<p>❌ Tutoría no especificada.</p>");
}

// 🔹 Obtener datos de la tutoría con su materia y docente
$sql = "SELECT t.*, m.nombre AS materia_nombre, u.nombre AS docente_nombre
        FROM tutorias t
        INNER JOIN materias m ON m.id = t.id_materia
        INNER JOIN usuarios u ON u.id = t.id_docente
        WHERE t.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_tutoria);
$stmt->execute();
$result = $stmt->get_result();
$tutoria = $result->fetch_assoc();

if (!$tutoria) {
  die("<p>⚠️ No se encontró la tutoría.</p>");
}

// 🔹 Obtener lista de inscritos (alumnos)
$sql2 = "SELECT i.*, a.nombre AS alumno_nombre, a.carnet
         FROM inscripciones i
         INNER JOIN usuarios a ON a.id = i.id_alumno
         WHERE i.id_tutoria = ?";
$stmt2 = $conexion->prepare($sql2);
$stmt2->bind_param("i", $id_tutoria);
$stmt2->execute();
$inscritos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$total = $tutoria['cupo_maximo'];
$reservados = count($inscritos);
$disponibles = max(0, $total - $reservados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Portal de Tutorías - Detalle de Tutoría</title>

  <link rel="stylesheet" href="../../CSS/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root{--bg:#f5f7fb;--card:#fff;--accent:#0f4db6;--muted:#6b6b6b}
    *{box-sizing:border-box;font-family:'Poppins',sans-serif}
    body{margin:0;background:var(--bg);color:#222}
    .site-header{display:flex;justify-content:space-between;align-items:center;padding:12px 20px;background:#fff;border-bottom:1px solid #eee}
    .brand-logo{height:34px}
    .container{max-width:820px;margin:28px auto;display:grid;grid-template-columns:1fr 380px;gap:18px;padding:0 12px}
    .card{background:var(--card);border-radius:12px;box-shadow:0 6px 18px rgba(15,77,182,0.04);padding:16px}
    h2.title{color:var(--accent);text-align:center;margin:6px 0 18px;font-size:22px}
    .tutoria-header{display:flex;flex-direction:column;gap:6px;margin-bottom:12px}
    .stats{display:flex;gap:8px;flex-wrap:wrap}
    .stat{background:#eef6ff;padding:8px 10px;border-radius:8px;font-weight:700;color:#0f4db6}
    .inscrito-list{display:flex;flex-direction:column;gap:8px;margin-top:12px}
    .inscrito{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:10px;border:1px solid #eef2fb;background:linear-gradient(180deg,#fff,#fbfdff)}
    .inscrito .left{display:flex;flex-direction:column}
    .inscrito .name{font-weight:700}
    .inscrito .meta{font-size:13px;color:#555}
    .btn{border:0;padding:8px 12px;border-radius:8px;cursor:pointer;font-weight:600}
    .btn-ghost{background:#eef2fb;color:#0f4db6}
    .empty{color:#777;text-align:center;padding:12px}
    footer.site-footer{grid-column:1/-1;padding:18px;text-align:center;color:#777;font-size:14px}
    @media (max-width:900px){.container{grid-template-columns:1fr;max-width:720px}}
  </style>
</head>
<body>
  <?php include(__DIR__ . "/../includes/header.php"); ?>

  <main class="container">
    <!-- PANEL PRINCIPAL -->
    <section class="card">
      <h2 class="title">Detalles de la Tutoría</h2>

      <div class="tutoria-header">
        <div style="font-weight:800;font-size:18px">
          <?= htmlspecialchars($tutoria['titulo']) ?> — <?= htmlspecialchars($tutoria['materia_nombre']) ?>
        </div>
        <div style="color:#555">
          <?= htmlspecialchars($tutoria['tipo'] === 'virtual' ? 'Virtual' : 'Presencial') ?> |
          <?= date('d/m/Y', strtotime($tutoria['fecha'])) ?> • 
          <?= substr($tutoria['hora_inicio'], 0, 5) ?> - <?= substr($tutoria['hora_fin'], 0, 5) ?>
        </div>
      </div>

      <p><?= nl2br(htmlspecialchars($tutoria['descripcion'])) ?></p>

      <hr style="margin:14px 0">

      <h3>Inscritos</h3>
      <div class="inscrito-list">
        <?php if (empty($inscritos)): ?>
          <div class="empty">No hay inscritos aún.</div>
        <?php else: ?>
          <?php foreach ($inscritos as $i): ?>
            <div class="inscrito">
              <div class="left">
                <div class="name"><?= htmlspecialchars($i['alumno_nombre']) ?></div>
                <div class="meta">
                  Carnet: <?= htmlspecialchars($i['carnet']) ?> • Inscrito el <?= date('d/m/Y H:i', strtotime($i['fecha_inscripcion'])) ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div style="margin-top:12px;display:flex;justify-content:flex-end">
        <a href="mis_tutorias.php" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Volver</a>
      </div>
    </section>

    <!-- SIDEBAR -->
    <aside class="card">
      <h3>Resumen</h3>
      <div style="font-weight:700"><?= htmlspecialchars($tutoria['materia_nombre']) ?></div>
      <div style="color:#555"><?= date('d/m/Y', strtotime($tutoria['fecha'])) ?></div>

      <div class="stats" style="margin-top:10px">
        <div class="stat">Total: <?= $total ?></div>
        <div class="stat">Reservados: <?= $reservados ?></div>
        <div class="stat">Disponibles: <?= $disponibles ?></div>
      </div>

      <div style="margin-top:10px;color:#6b7280;font-size:14px">
        Tipo: <?= htmlspecialchars(ucfirst($tutoria['tipo'])) ?><br>
        Lugar / Plataforma: <?= htmlspecialchars($tutoria['lugar'] ?: $tutoria['plataforma'] ?: '—') ?>
      </div>
    </aside>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
  </main>
</body>
</html>
