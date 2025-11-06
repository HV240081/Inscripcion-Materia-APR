<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$carnet = $_SESSION["carnet"] ?? "Invitado";
?>

<header class="site-header">
  <div class="header-container">
    <!-- Logo -->
    <div class="brand">
      <img src="../../IMG/UDB_horizontal.png" alt="UDB" class="brand-logo">
    </div>

    <!-- Sección derecha -->
    <div class="right-section">
      <!-- Notificaciones -->
      <div class="notification-menu" id="notificationMenu">
        <i class="fas fa-bell"></i>
        <span class="notification-badge" id="notifCount" style="display:none;">0</span>
        <div class="notification-dropdown" id="notifDropdown">
          <h4>Notificaciones</h4>
          <ul id="notifList">
            <li class="no-notif">Cargando...</li>
          </ul>
        </div>
      </div>

      <!-- Usuario -->
      <div class="user-menu" id="userMenu">
        <div class="user-info">
          <i class="fas fa-user-circle user-icon"></i>
          <span><?php echo htmlspecialchars($carnet); ?></span>
        </div>
        <div class="dropdown" id="userDropdown">
          <a href="../../../Backend/logout.php">Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
</header>

<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>

<style>
  /* ===== HEADER ===== */
  .site-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e0e0e0;
    padding: 10px 2vw; /* ← margen lateral relativo al ancho de la pantalla */
    position: sticky;
    top: 0;
    z-index: 100;
    width: 100%;
  }

  .header-container {
    display: flex;
    justify-content: space-between; /* ← separa automáticamente según ancho */
    align-items: center;
    max-width: 1600px; /* ← evita que se estire demasiado en pantallas grandes */
    margin: 0 auto;
    width: 100%;
  }

  .brand-logo {
    height: 55px;
    width: auto;
  }

  .right-section {
    display: flex;
    align-items: center;
    gap: 1.5rem; /* ← espacio adaptable entre íconos */
  }

  /* ===== NOTIFICACIONES ===== */
  .notification-menu {
    position: relative;
    cursor: pointer;
  }

  .notification-menu i {
    font-size: 22px;
    color: #1e2a78;
    transition: color 0.2s;
  }

  .notification-menu i:hover {
    color: #3949ab;
  }

  .notification-badge {
    background-color: #e74c3c;
    color: white;
    border-radius: 50%;
    padding: 2px 7px;
    font-size: 11px;
    position: absolute;
    top: -6px;
    right: -10px;
  }

  .notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 35px;
    width: 280px;
    background-color: #fff;
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 10px;
    z-index: 10;
  }

  .notification-menu.open .notification-dropdown {
    display: block;
  }

  .notification-dropdown h4 {
    margin: 0 0 10px 0;
    font-size: 15px;
    color: #1e2a78;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 5px;
  }

  #notifList {
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 200px;
    overflow-y: auto;
  }

  #notifList li {
    padding: 8px 5px;
    border-bottom: 1px solid #f1f1f1;
    font-size: 14px;
    color: #333;
  }

  #notifList li.no-notif {
    text-align: center;
    color: #888;
    border: none;
  }

  /* ===== USUARIO ===== */
  .user-menu {
    position: relative;
    cursor: pointer;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #1e2a78;
    font-weight: 500;
    background: none;
    padding: 6px 8px;
    border-radius: 6px;
  }

  .user-info:hover {
    color: #3949ab;
  }

  .user-icon {
    font-size: 20px;
  }

  .dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 35px;
    background: white;
    border-radius: 6px;
    border: 1px solid #ddd;
    min-width: 140px;
    z-index: 20;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }

  .dropdown a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
    transition: background 0.2s;
  }

  .dropdown a:hover {
    background-color: #f1f1f1;
  }

  .user-menu.open .dropdown {
    display: block;
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 768px) {
    .brand-logo {
      height: 45px;
    }

    .right-section {
      gap: 1rem;
    }

    .notification-dropdown,
    .dropdown {
      right: -20px;
    }
  }
</style>

<script>
  const notifMenu = document.getElementById('notificationMenu');
  const userMenu = document.getElementById('userMenu');
  const notifList = document.getElementById('notifList');
  const notifCount = document.getElementById('notifCount');

  notifMenu.addEventListener('click', e => {
    e.stopPropagation();
    notifMenu.classList.toggle('open');
    userMenu.classList.remove('open');
  });

  userMenu.addEventListener('click', e => {
    e.stopPropagation();
    userMenu.classList.toggle('open');
    notifMenu.classList.remove('open');
  });

  window.addEventListener('click', () => {
    notifMenu.classList.remove('open');
    userMenu.classList.remove('open');
  });

  async function cargarNotificaciones() {
    try {
      const resp = await fetch('/APR%20PROYECTO/Backend/controllers/notificaciones.php', {
        method: 'GET',
        credentials: 'include'
      });
      if (!resp.ok) throw new Error('Error al cargar notificaciones');
      const data = await resp.json();

      notifList.innerHTML = "";
      if (!data.success || data.notificaciones.length === 0) {
        notifList.innerHTML = '<li class="no-notif">No tienes notificaciones.</li>';
        notifCount.style.display = "none";
      } else {
        data.notificaciones.slice(0, 10).forEach(n => {
          const li = document.createElement('li');
          li.innerHTML = `<strong>${n.mensaje}</strong><br><small>${n.fecha}</small>`;
          notifList.appendChild(li);
        });
        notifCount.textContent = data.notificaciones.length;
        notifCount.style.display = "inline";
      }
    } catch (error) {
      notifList.innerHTML = '<li class="no-notif">Error al obtener notificaciones.</li>';
      notifCount.style.display = "none";
      console.error(error);
    }
  }

  cargarNotificaciones();
  setInterval(cargarNotificaciones, 60000);
</script>
