<?php
include(__DIR__ . "/../data/conexion.php");
// Obtenemos todas las categorías únicas
$stmtCats = $con->prepare("SELECT nombre FROM categorias");
$stmtCats->execute();
$resultCats = $stmtCats->get_result();
$categorias = $resultCats->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CatInk News</title>
  <!-- Local CSS-->
  <link rel="stylesheet" href="/CatInk_Proyecto/CSS/styles.css">
  <!-- Iconos: Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
  <script src="https://unpkg.com/quill-image-resize-module/image-resize.min.js"></script>
</head>
<body>
<nav class="navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="/CatInk_Proyecto/index.php">
      <img id="logo" src="/CatInk_Proyecto/img/logo_alt.jpg" alt="CatInk Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav nav-left">
        <li class="nav-item"><a class="nav-link active" href="/CatInk_Proyecto/index.php">Home</a></li>
        <?php foreach ($categorias as $cat): ?>
          <li class="nav-item">
            <a class="nav-link" href="/CatInk_Proyecto/views/categoria.php?cat=<?= urlencode($cat['nombre']) ?>">
              <?= htmlspecialchars($cat['nombre']) ?>
            </a>
          </li>
        <?php endforeach; ?>
        <li class="nav-item d-flex gap-2 align-items-center">
            <!-- BOTÓN MODO OSCURO -->
            <button id="themeToggle" class="btn btn-outline-secondary">🌙</button>
            <a href="/CatInk_Proyecto/views/login.php" class="btn btn-outline-secondary"><span class="bi bi-person-fill"></span></a>
        </li>
      </ul>
      <form class="nav-search" onsubmit="return false;">
        <i class="bi bi-search"></i>
        <input
          type="search"
          id="searchInput"
          placeholder="Buscar noticias..."
          autocomplete="off"
        >
      </form>
    </div>
  </div>
</nav>
<!-- Inicio del contenido principal de la página -->
<main class="site-main">
