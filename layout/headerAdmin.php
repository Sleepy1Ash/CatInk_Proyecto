<?php
    session_start();
    if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
    }
    $usuario = $_SESSION['usuario'];
    include("./../data/conexion.php");
    $sql="select * from usuarios where usuario='$usuario'";
    $result=mysqli_query($con,$sql);
    $fila=$result->fetch_assoc();
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CatInk News</title>

  <!-- Local CSS (replacement for Bootstrap) -->
  <link rel="stylesheet" href="/CatInk_Proyecto/CSS/styles.css">
  <!-- Iconos: Bootstrap Icons (se usan las clases .bi en el HTML) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
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
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">News</a></li>
        <li class="nav-item"><a class="nav-link" href="#">About us</a></li>
      </ul>
      <div class="row">
        <div class="col">
          <!-- BOTÓN MODO OSCURO -->
          <button id="themeToggle" class="btn btn-outline-secondary">🌙</button>
        </div>
        <div class="col">
          <a href="#" class="btn btn-outline-secondary"><?php echo $fila['usuario'] ?></a>
        </div>
      </div>
    </div>
  </div>
<!-- Cabecera panel admin: navegación y acciones del usuario -->
</nav>
<!-- Inicio del contenido principal para zonas de administración -->
<main class="site-main">
