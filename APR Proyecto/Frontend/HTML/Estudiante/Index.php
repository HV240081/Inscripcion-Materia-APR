<?php
session_start();

// Verificar sesión activa
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "alumno") {
  header("Location: ../../../index.html");
  exit();
}

$nombre = $_SESSION["nombre"];
$carnet = $_SESSION["carnet"];
$rol = $_SESSION["rol"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal de Tutorías - Home</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="../../CSS/style.css">
    <style>
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
    .options {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 60px;
    }
    .option-card {
      background-color: #eef1f6;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      width: 250px;
      transition: all 0.3s;
      cursor: pointer;
    }
    .option-card:hover {
      transform: translateY(-5px);
    }
    .option-card i {
      font-size: 50px;
      color: #4169e1;
      margin-bottom: 15px;
    }
  </style>
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>

</head>
<body>
  <!-- Header -->
    <?php include(__DIR__ . "/../includes/header.php"); ?>
  <!-- Hero Section -->
  <section class="hero">
    <h1>Bienvenido a tu portal de tutorías</h1>
    <p>Accede a tus tutorías disponibles, inscríbete y gestiona tus sesiones desde cualquier lugar, de manera rápida y sencilla.</p>
    <div class="hero-buttons">
      <a href="TutoriasDisponibles.php" class="btn-inscribir">Inscribir</a>
      <a href="MisInscripciones.php" class="btn-ver">Ver inscritas</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <div class="feature-card">
      <i class="fas fa-book-open"></i>
      <h3>Material actualizado</h3>
      <p>Accede a los contenidos más recientes y completos de tus materias.</p>
    </div>
    <div class="feature-card">
      <i class="fas fa-clock"></i>
      <h3>Gestión de tiempo</h3>
      <p>Organiza tus sesiones y optimiza tu planificación académica.</p>
    </div>
    <div class="feature-card">
      <i class="fas fa-chalkboard-teacher"></i>
      <h3>Profesores expertos</h3>
      <p>Recibe tutorías de docentes especializados y experimentados.</p>
    </div>
  </section>

  <!-- How it works Section -->
  <section class="how-it-works">
    <h2>Cómo funciona</h2>
    <div class="steps">
      <div class="step-card">
        <i class="fas fa-search"></i>
        <h4>Explora tutorías</h4>
        <p>Busca la materia que deseas reforzar y descubre las tutorías disponibles.</p>
      </div>
      <div class="step-card">
        <i class="fas fa-calendar-check"></i>
        <h4>Inscríbete</h4>
        <p>Selecciona la sesión que más te convenga y confirma tu inscripción.</p>
      </div>
      <div class="step-card">
        <i class="fas fa-graduation-cap"></i>
        <h4>Aprende y mejora</h4>
        <p>Asiste a tus tutorías, resuelve dudas y fortalece tus conocimientos.</p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include(__DIR__ . "/../includes/footer.php"); ?>

  <script>
    const menu = document.getElementById('userMenu');
    menu.addEventListener('click', () => {
      menu.classList.toggle('open');
    });
    window.addEventListener('click', (e) => {
      if (!menu.contains(e.target)) menu.classList.remove('open');
    });
  </script>
</body>
</html>
