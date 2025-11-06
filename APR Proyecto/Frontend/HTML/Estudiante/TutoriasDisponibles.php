<?php
session_start();
// Ajusta la redirección según tu estructura (ya lo tenías así)
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "alumno") {
    header("Location: ../../../index.html");
    exit();
}

$idEstudiante = intval($_SESSION['id_usuario']);
$carnet = $_SESSION['carnet'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tutorías Disponibles — Estudiante</title>

  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>

  <style>
    :root { --bg:#f5f7fb; --card:#fff; --accent:#0f4db6; --muted:#6b6b6b; }
    * { box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { margin:0; background:var(--bg); color:#222; padding:18px; }

    .page { max-width:1100px; margin: 0 auto; }
    header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .brand-logo { height:34px; }
    .user-info { background:#eef1f6; padding:8px 12px; border-radius:10px; color:#1e2a78; font-weight:600; }

    .grid { display:grid; grid-template-columns: 360px 1fr; gap:18px; align-items:start; }
    @media (max-width:920px){ .grid { grid-template-columns:1fr; } }

    /* Filtro card */
    .card { background:var(--card); padding:18px; border-radius:12px; box-shadow:0 6px 18px rgba(15,77,182,0.04); }
    .controls .row { display:flex; gap:8px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
    .nav-btn { padding:8px 10px; border-radius:8px; border:1px solid #dfe7f5; background:#fff; cursor:pointer; }
    .nav-btn:active{ transform: translateY(1px); }

    /* Date and arrows area */
    .date-area { display:flex; gap:8px; align-items:center; margin-bottom:10px; }
    #datePrev, #dateNext { width:40px; height:40px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:18px; }

    input[type="date"] { padding:8px 10px; border-radius:8px; border:1px solid #d0d5dd; background:#fff; }

    /* Select materia - ensure visible */
    #subjectSelect {
      width:100%;
      min-width: 200px;
      padding:10px;
      border-radius:8px;
      border:1px solid #d0d5dd;
      background:#fff;
      appearance: auto;
      z-index: 9999;
    }

    .pretty-date { font-weight:700; color:#374151; margin: 10px 0; text-transform: capitalize; }
    .helper { color:#6b7280; font-size:13px; margin-top:8px; }

    /* Tabla schedule */
    .schedule-table { width:100%; border-collapse:collapse; font-size:14px; margin-top:0; }
    .schedule-table th, .schedule-table td { padding:10px; border-bottom:1px solid #f0f3fb; text-align:left; vertical-align:middle; }
    .schedule-table th { background:transparent; color:#4b5563; font-weight:700; }
    .slot-row { transition: background .12s; }
    .slot-row.selected { background: linear-gradient(90deg, rgba(15,77,182,0.06), transparent); }
.small-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 85px;
  height: 26px;
  padding: 0 10px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 999px;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  line-height: 1;
}

.badge-available {
  background: #10b981; /* verde */
}

.badge-full {
  background: #ef4444; /* rojo */
}
    .btn-inscribirse { padding:8px 10px; border-radius:8px; border:0; cursor:pointer; font-weight:700; background:#0f4db6; color:#fff; }
    .btn-inscribirse[disabled] { background:#ccc; cursor:not-allowed; color:#666; }
    .empty { padding:20px; text-align:center; color:#6b7280; }

    footer.site-footer{ margin-top:18px; text-align:center; color:#777; font-size:14px; }
  </style>
</head>
<body>
  <div class="page">
    <?php include(__DIR__ . "/../includes/header.php"); ?>

    <div class="grid">
      <!-- FILTROS -->
      <section class="card controls" aria-label="Filtros">
        <!-- Date arrows arriba -->
                   <label for="subjectSelect" style="font-weight:600; display:block; margin-bottom:6px;">Fecha:</label>
        <div class="date-area" role="group" aria-label="Selector de fecha">
          <button id="datePrev" class="nav-btn" title="Día anterior">&lt;</button>
          <input id="datePicker" type="date" aria-label="Seleccionar fecha">
          <button id="dateNext" class="nav-btn" title="Día siguiente">&gt;</button>
        </div>

        <!-- Select materia debajo -->
        <div style="margin-top:8px;">
          <label for="subjectSelect" style="font-weight:600; display:block; margin-bottom:6px;">Materia:</label>
          <select id="subjectSelect" aria-label="Seleccionar materia">
            <option value="0">-- Cargando materias --</option>
          </select>
        </div>

        <!-- Fecha bonita y helper text -->
        <div class="pretty-date" id="prettyDate" aria-live="polite"></div>
        <div class="helper">Selecciona una materia y una fecha para ver las sesiones disponibles. Pulsa "Reservar" en la fila correspondiente.</div>
      </section>

      <!-- TABLA -->
      <section class="card schedule" aria-label="Horario de tutorías">

        <div id="scheduleContainer" role="region" aria-live="polite">
          <table class="schedule-table" id="scheduleTable">
            <thead>
              <tr>
                <th>Hora</th>
                <th>Materia</th>
                <th>Modalidad</th>
                <th>Cupos</th>
                <th>Docente</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="scheduleBody">
              <tr><td colspan="6" class="empty">Seleccione una materia y una fecha para ver las tutorías.</td></tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <?php include(__DIR__ . "/../includes/footer.php"); ?>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const API_MATERIAS = "../../../Backend/controllers/listar_materias.php";
  const API_TUTORIAS  = "../../../Backend/controllers/listar_tutorias.php";
  const API_INSCRIBIR = "../../../Backend/controllers/inscripciones_controlador.php";

  const subjectSelect = document.getElementById('subjectSelect');
  const datePicker = document.getElementById('datePicker');
  const prettyDate = document.getElementById('prettyDate');
  const scheduleBody = document.getElementById('scheduleBody');
  const datePrev = document.getElementById('datePrev');
  const dateNext = document.getElementById('dateNext');
  // reserveBtn may not exist in your markup; get it but check before using
  const reserveBtn = document.getElementById('reserveBtn');

  // id estudiante desde PHP (asegúrate que la variable PHP $idEstudiante esté definida)
  const idEstudiante = <?php echo json_encode($idEstudiante ?? 0); ?>;

  // Inicializar fecha hoy
  const today = new Date();
  datePicker.value = today.toISOString().slice(0,10);
  prettyDate.textContent = formatPretty(datePicker.value);

  // Cargar materias en select
  fetch(API_MATERIAS)
    .then(r => r.json())
    .then(list => {
      subjectSelect.innerHTML = '<option value="0">-- Todas las materias --</option>';
      list.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.nombre;
        subjectSelect.appendChild(opt);
      });
      // cargar tutorías tras cargar materias
      cargarTutorias();
    })
    .catch(err => {
      console.error("Error cargando materias:", err);
      subjectSelect.innerHTML = '<option value="0">-- Error cargando materias --</option>';
    });

  // Utilidades
  function formatPretty(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('es-ES', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
  }
  function escapeHtml(str){ if (!str && str !== 0) return ''; return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }

  // Navegación de días
  datePrev.addEventListener('click', () => { shiftDate(-1); });
  dateNext.addEventListener('click', () => { shiftDate(1); });
  function shiftDate(deltaDays) {
    const cur = new Date(datePicker.value + 'T00:00:00');
    cur.setDate(cur.getDate() + deltaDays);
    datePicker.value = cur.toISOString().slice(0,10);
    prettyDate.textContent = formatPretty(datePicker.value);
    cargarTutorias();
  }

  datePicker.addEventListener('change', () => {
    prettyDate.textContent = formatPretty(datePicker.value);
    cargarTutorias();
  });
  subjectSelect.addEventListener('change', cargarTutorias);

  // Si reserveBtn existe, añadimos el listener (si no, lo ignoramos)
  let selectedRowId = null;
  if (reserveBtn) {
    reserveBtn.addEventListener('click', () => {
      if (!selectedRowId) {
        alert("Selecciona una tutoría primero (haz clic en la fila).");
        return;
      }
      reservar(selectedRowId);
    });
  }

  // Cargar tutorías
  function cargarTutorias() {
    const materiaId = subjectSelect.value || 0;
    const fecha = datePicker.value || '';

    scheduleBody.innerHTML = '<tr><td colspan="6" class="empty">Cargando tutorías...</td></tr>';

    const url = `${API_TUTORIAS}?materia_id=${encodeURIComponent(materiaId)}&fecha=${encodeURIComponent(fecha)}`;

    fetch(url)
      .then(async res => {
        if (!res.ok) {
          const txt = await res.text();
          throw new Error(`HTTP ${res.status}: ${txt}`);
        }
        return res.json();
      })
      .then(data => {
        selectedRowId = null;
        renderTabla(data);
      })
      .catch(err => {
        console.error("Error al cargar tutorías:", err);
        scheduleBody.innerHTML = `<tr><td colspan="6" class="empty">Error cargando tutorías.</td></tr>`;
      });
  }

  function renderTabla(items) {
    scheduleBody.innerHTML = '';
    if (!Array.isArray(items) || items.length === 0) {
      scheduleBody.innerHTML = '<tr><td colspan="6" class="empty">No hay tutorías para la materia y fecha seleccionadas.</td></tr>';
      return;
    }

    items.forEach(t => {
      const tr = document.createElement('tr');
      tr.className = 'slot-row';
      tr.dataset.id = t.id;

      const disponibles = Number(t.cupos_restantes);

      tr.innerHTML = `
        <td><strong>${escapeHtml(t.hora)}</strong></td>
        <td>${escapeHtml(t.materia)}</td>
        <td>${escapeHtml(t.modalidad)}</td>
        <td><span class="small-badge ${disponibles>0?'badge-available':'badge-full'}">${disponibles>0?disponibles+' disponibles':'Sin cupos'}</span></td>
        <td>${escapeHtml(t.docente)}</td>
        <td style="text-align:right">
          <button class="btn-inscribirse" ${disponibles>0?'':'disabled'}>${disponibles>0?'Inscribirse':'Lleno'}</button>
        </td>
      `;

      tr.addEventListener('click', () => {
        document.querySelectorAll('.slot-row').forEach(r => r.classList.remove('selected'));
        tr.classList.add('selected');
        selectedRowId = t.id;
      });

      // Aquí añadimos el listener al botón (si el script NO se rompió antes)
      const btn = tr.querySelector('button');
      if (btn) {
        btn.addEventListener('click', (ev) => {
          ev.stopPropagation();
          if (disponibles <= 0) { alert('No hay cupos disponibles.'); return; }
          reservar(t.id);
        });
      }

      scheduleBody.appendChild(tr);
    });
  }

  // Funcion reservar (verifica y manda el FormData)
  async function reservar(idTutoria) {
    if (!confirm("¿Deseas inscribirte en esta tutoría?")) return;

    // debug: asegurar que idEstudiante tenga valor
    if (!idEstudiante || idEstudiante === 0) {
      alert("Error: id de estudiante no encontrado en la sesión. Vuelve a iniciar sesión.");
      console.error("idEstudiante:", idEstudiante);
      return;
    }

    const fd = new FormData();
    fd.append('id_tutoria', idTutoria);
    fd.append('id_estudiante', idEstudiante);

    // DEBUG: mostrar lo que se envía en la petición (ver consola)
    for (const pair of fd.entries()) {
      console.log(pair[0]+ ': ' + pair[1]);
    }

    try {
      const res = await fetch(API_INSCRIBIR, { method: 'POST', body: fd });
      if (!res.ok) {
        const txt = await res.text();
        throw new Error(`HTTP ${res.status}: ${txt}`);
      }
      const resp = await res.json();
      console.log("Respuesta inscripción:", resp);
      alert(resp.msg || 'Respuesta recibida.');
      if (resp.status === 'ok') cargarTutorias();
    } catch (err) {
      console.error("Error al inscribir:", err);
      alert("Ocurrió un error al intentar inscribirte.");
    }
  }

  // carga inicial
  cargarTutorias();
});
</script>

</body>
</html>
