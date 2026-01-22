<?php
    session_start();
    if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
    }
    $usuario = $_SESSION['usuario'];
    include("./../data/conexion.php");
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $fila = $result->fetch_assoc();
?>
<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CatInk News</title>
  <!-- Local CSS (replacement for Bootstrap) -->
  <link rel="stylesheet" href="/CatInk_Proyecto/CSS/styles.css">
  <link rel="stylesheet" href="/CatInk_Proyecto/CSS/admin.css">
  <!-- Iconos: Bootstrap Icons (se usan las clases .bi en el HTML) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
  <script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://unpkg.com/quill-image-resize-module/image-resize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
</head>
<body class="has-sidebar">
<div class="sidebar">
    <div class="logotipo">
        <a href="./../../CatInk_Proyecto/views/admin.php"><img id="icon" src="./../../CatInk_Proyecto/img/logo_alt.jpg" alt="Logo"></a>
    </div>
    <div id="user">
        <h4><?php echo $fila['usuario'] ?></h4>
    </div>
    <ul class="sidebar-menu">
        <li class="sidebar-menu-item">
            <a href="./../../CatInk_Proyecto/views/admin.php" class="sidebar-menu-link"><i class="bi bi-house"></i> Inicio</a>
        </li>
        <hr>
        <li class="sidebar-menu-item">
            <a href="./../../CatInk_Proyecto/views/contenidos.php" class="sidebar-menu-link">Administrar</a>
    </ul>
    <div class="sidebar-footer">
      <button id="themeToggle" class="btn btn-icon" title="Cambiar tema">🌙</button>
      <a href="./../../CatInk_Proyecto/controllers/logoutcontroller.php" class="sidebar-menu-link"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>
</div>
<!-- Inicio del contenido principal para zonas de administración -->
<main class="site-main">
