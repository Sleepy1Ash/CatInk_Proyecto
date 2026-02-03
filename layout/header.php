<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CatInk News</title>

  <!-- Local CSS-->
  <link rel="stylesheet" href="/CatInk_Proyecto/CSS/styles.css">
  <!-- Iconos: Bootstrap Icons (se usan las clases .bi en el HTML) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://unpkg.com/quill-image-resize-module/image-resize.min.js"></script>
</head>
<body>
<!-- Cabecera principal: navegación, logo y acciones (tema, login) -->
<nav class="navbar">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img id="logo" src="./../../CatInk_Proyecto/img/logo_alt.jpg" alt="CatInk Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav nav-left">
        <li class="nav-item"><a class="nav-link active" href="./../../CatInk_Proyecto/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="./../../CatInk_Proyecto/views/categoria.php?cat=Peliculas">Peliculas</a></li>
        <li class="nav-item"><a class="nav-link" href="./../../CatInk_Proyecto/views/categoria.php?cat=Series">Series</a></li>
        <li class="nav-item"><a class="nav-link" href="./../../CatInk_Proyecto/views/categoria.php?cat=Cultura Pop">Cultura Pop</a></li>
        <li class="nav-item"><a class="nav-link" href="./../../CatInk_Proyecto/views/categoria.php?cat=Anime">Anime</a></li>
      </ul>
      <div class="row">
        <div class="col">
          <!-- BOTÓN MODO OSCURO -->
          <button id="themeToggle" class="btn btn-outline-secondary">🌙</button>
        </div>
        <div class="col">
          <a href="views/login.php" class="btn btn-outline-secondary"><span class="bi bi-person-fill"></span></a>
        </div>
      </div>
    </div>
  </div>
</nav>
  <!-- Inicio del contenido principal de la página -->
<main class="site-main">
