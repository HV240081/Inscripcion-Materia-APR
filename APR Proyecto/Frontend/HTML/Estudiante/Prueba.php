<?php
session_start();

// DEBUG DEV: mostrar errores en desarrollo (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar sesión activa y rol alumno (en tu BD el rol es 'alumno')
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "alumno") {
  // Redirige al login si no está autenticado como alumno
  header("Location: ../../../Backend/logout.php");
  exit();
}

$id_estudiante = $_SESSION["id_usuario"];
$carnet = $_SESSION["carnet"] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tutorías Disponibles — Estudiante</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f5f7fb; margin: 0; }
    header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 10px 20px; border-bottom: 1px solid #ddd; }
    .brand-logo { height: 34px; }
    .user-info { background: #eef1f6; padding: 10px 15px; border-radius: 10px; font-weight: 600; color: #1e2a78; }
    main { max-width: 900px; margin: 30px auto; padding: 0 15px; }
    h1 { color: #0f4db6; text-align: center; margin-bottom: 25px; }
    .tutoria-card { background: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 16px; margin-bottom: 15px; }
    .tutoria-card h3 { color: #0f4db6; margin: 0 0 8px; }
    .meta { color: #555; font-size: 14px; margin-bottom: 10px; }
    .btn { border: none; padding: 8px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-primary { background: #0f4db6; color: #fff; }
    .btn-primary:hover { background: #0d3f99; }
    .btn-disabled { background: #ccc; color: #555; cursor: not-allowed; }
    .message { text-align:center; color:#777; padding: 16px; }
    footer { text-align: center; color: #777; padding: 20px; font-size: 14px; background: #fff; border-top: 1px solid #ddd; margin-top: 30px; }
  </style>
</head>
<body>
  <header>
    <img src="../../IMG/UDB_horizontal.png" alt="UDB" class="brand-logo">
    <div class="user-info"><?php echo htmlspecialchars($carnet); ?></div>
  </header>

  <main>
    <h1>Tutorías Disponibles</h1>
    <div id="listaTutorias" class="message">Cargando tutorías...</div>
  </main>

  <footer>
    © <?php echo date('Y'); ?> Universidad Don Bosco — Portal de Tutorías
  </footer>

  <script>
    const idEstudiante = <?php echo json_encode($id_estudiante, JSON_HEX_TAG); ?>;
    const listaTutorias = document.getElementById('listaTutorias');

    // Ajusta estas rutas si la estructura de carpetas en tu servidor es diferente
    const URL_LISTAR = "../../../Backend/controllers/listar_tutorias.php";
    const URL_INSCRIBIR = "../../../Backend/controllers/inscripciones_controlador.php";

    // Cargar tutorías desde backend
    fetch(URL_LISTAR)
      .then(async res => {
        if (!res.ok) {
          const txt = await res.text();
          throw new Error(`Error HTTP ${res.status}: ${txt}`);
        }
        return res.json();
      })
      .then(data => {
        if (!Array.isArray(data) || data.length === 0) {
          listaTutorias.innerHTML = "<div class='message'>No hay tutorías disponibles actualmente.</div>";
          return;
        }

        listaTutorias.innerHTML = data.map(t => `
          <div class="tutoria-card">
            <h3>${escapeHtml(t.materia)}</h3>
            <div class="meta">📅 ${escapeHtml(t.fecha)} — 🕒 ${escapeHtml(t.hora)}<br>📍 ${escapeHtml(t.modalidad)} — Cupos restantes: ${Number(t.cupos_restantes)}</div>
            <p>${escapeHtml(t.descripcion || "")}</p>
            <button 
              class="btn ${t.cupos_restantes > 0 ? 'btn-primary' : 'btn-disabled'}" 
              ${t.cupos_restantes > 0 ? `onclick="inscribirse(${t.id})"` : "disabled"}
            >
              ${t.cupos_restantes > 0 ? "Inscribirme" : "Cupo lleno"}
            </button>
          </div>
        `).join('');
      })
      .catch(err => {
        console.error("Fallo al cargar tutorías:", err);
        listaTutorias.innerHTML = `<div class="message">Error cargando tutorías: ${escapeHtml(err.message)}</div>`;
      });

    // Inscribirse
    function inscribirse(idTutoria) {
      if (!confirm("¿Deseas inscribirte en esta tutoría?")) return;

      const formData = new FormData();
      formData.append('id_tutoria', idTutoria);
      formData.append('id_estudiante', idEstudiante);

      fetch(URL_INSCRIBIR, { method: 'POST', body: formData })
        .then(async res => {
          if (!res.ok) {
            const txt = await res.text();
            throw new Error(`Error HTTP ${res.status}: ${txt}`);
          }
          return res.json();
        })
        .then(resp => {
          alert(resp.msg || "Respuesta recibida.");
          if (resp.status === "ok") location.reload();
        })
        .catch(err => {
          console.error("Error al inscribirse:", err);
          alert("Ocurrió un error al intentar inscribirte: " + err.message);
        });
    }

    // función para escapar HTML simple
    function escapeHtml(unsafe) {
      if (unsafe === undefined || unsafe === null) return '';
      return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  </script>
</body>
</html>
