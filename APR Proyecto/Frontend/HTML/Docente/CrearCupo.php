<?php
session_start();

// Verificar sesión activa y rol docente
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "docente") {
  header("Location: ../../../Backend/logout.php");
  exit();
}

$nombre = $_SESSION["nombre"];
$carnet = $_SESSION["carnet"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Cupo — Tutorías UDB</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="../../CSS/style.css">

  <style>
    /* --- FORMULARIO --- */
    .form-container {
      max-width: 700px;
      margin: 40px auto;
      background: #f5f7fb;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    label { font-weight: 600; }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }
    .btn-submit {
      background-color: #4169e1;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-submit:hover {
      background-color: #1e3fa1;
    }

    /* --- USER MENU --- */
    .user-menu {
      position: relative;
      display: inline-block;
    }
    .user-info {
      cursor: pointer;
      font-weight: 600;
      color: #1e2a78;
      background: #eef1f6;
      padding: 10px 14px;
      border-radius: 10px;
    }
    .dropdown {
      display: none;
      position: absolute;
      right: 0;
      background: white;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      border-radius: 8px;
      margin-top: 8px;
      min-width: 150px;
      z-index: 10;
    }
    .dropdown a {
      display: block;
      padding: 10px;
      text-decoration: none;
      color: #333;
    }
    .dropdown a:hover {
      background-color: #f1f1f1;
    }
    .user-menu.open .dropdown {
      display: block;
    }

    /* --- FOOTER --- */
    .site-footer {
      text-align: center;
      color: #777;
      padding: 20px;
      font-size: 14px;
      border-top: 1px solid #ddd;
      margin-top: 40px;
      background-color: #fff;
    }
  </style>
  <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>
</head>
<body>
  <!-- HEADER -->
  <?php include(__DIR__ . "/../includes/header.php"); ?>

  <!-- MAIN CONTENT -->
  <main>
    <section class="form-container">
      <h1>Crear Cupo de Tutoría</h1>

      <form action="../../../Backend/controllers/crear_cupo.php" method="POST">

        <div class="form-group">
          <label for="titulo">Título o tema</label>
          <input type="text" id="titulo" name="titulo" placeholder="Ej: Repaso de Álgebra Lineal" required>
        </div>

        <div class="form-group">
          <label for="materia">Materia</label>
          <select id="materia" name="materia" required>
            <option value="">Seleccione una materia</option>
            <?php
            require_once("../../../Backend/config/conexion.php");
            $query = "SELECT id, nombre FROM materias ORDER BY nombre ASC";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
              echo '<option value="'.$row["id"].'">'.htmlspecialchars($row["nombre"]).'</option>';
            }
            $conn->close();
            ?>
          </select>
        </div>

        <div class="form-group">
          <label for="fecha">Fecha</label>
          <input type="date" id="fecha" name="fecha" required>
        </div>

        <div class="form-group">
          <label for="horaInicio">Hora de Inicio</label>
          <input type="time" id="horaInicio" name="horaInicio" min="07:00" max="19:00" required>
        </div>

        <div class="form-group">
          <label for="horaFin">Hora de Fin</label>
          <input type="time" id="horaFin" name="horaFin" min="07:00" max="21:00" required>
        </div>

        <div class="form-group">
          <label for="tipo">Tipo de tutoría</label>
          <select id="tipo" name="tipo" required>
            <option value="">Seleccione tipo</option>
            <option value="presencial">Presencial</option>
            <option value="virtual">Virtual</option>
          </select>
        </div>

        <div class="form-group">
          <label for="lugar">Lugar / Plataforma</label>
          <input type="text" id="lugar" name="lugar" placeholder="Ej: Aula 4B o Zoom" required>
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción o comentarios</label>
          <textarea id="descripcion" name="descripcion" rows="3" placeholder="Ej: Revisaremos ejercicios del parcial 1"></textarea>
        </div>

        <div class="form-group">
          <label for="cupo">Cupo máximo (máx. 20 estudiantes)</label>
          <input type="number" id="cupo" name="cupo" min="1" max="20" placeholder="Ej: 10" required>
        </div>

        <button type="submit" class="btn-submit">Crear Cupo</button>
      </form>
    </section>
  </main>

  <!-- FOOTER -->
  <?php include(__DIR__ . "/../includes/footer.php"); ?>

  <script>
    // Fecha mínima = hoy
    const fechaInput = document.getElementById("fecha");
    const hoy = new Date().toISOString().split("T")[0];
    fechaInput.min = hoy;

    // Validar hora fin > hora inicio
    const horaInicio = document.getElementById("horaInicio");
    const horaFin = document.getElementById("horaFin");
    const form = document.querySelector("form");

    form.addEventListener("submit", (e) => {
      if (horaFin.value <= horaInicio.value) {
        alert("La hora de fin debe ser posterior a la hora de inicio.");
        e.preventDefault();
      }
      if (fechaInput.value < hoy) {
        alert("La fecha no puede ser anterior al día actual.");
        e.preventDefault();
      }
    });

    // Menú desplegable del usuario
    const menu = document.getElementById('userMenu');
    menu.addEventListener('click', () => {
      menu.classList.toggle('open');
    });
    window.addEventListener('click', (e) => {
      if (!menu.contains(e.target)) menu.classList.remove('open');
    });

    // Año dinámico del footer
    document.getElementById("year").textContent = new Date().getFullYear();
  </script>
</body>
</html>
