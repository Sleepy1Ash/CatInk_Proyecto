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

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    #logo {
        border-radius: 50%;
        max-height: 64px;     /* altura máxima dentro del navbar */
        width: auto;          /* mantiene proporción */
        height: auto;
        object-fit: contain;  /* asegura que no se recorte */
    }
    .site-footer {
      background-color: var(--bs-body-bg);
      border-top: 1px solid rgba(255,255,255,0.1);
    }
    [data-bs-theme="light"] .site-footer {
      border-top: 1px solid rgba(0,0,0,0.1);
    }
    .footer-title {
      font-weight: 700;
      margin-bottom: 15px;
    }
    .footer-text {
      font-size: 0.95rem;
      opacity: 0.85;
    }
    .footer-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .footer-links li {
      margin-bottom: 8px;
    }
    .footer-links a {
      text-decoration: none;
      color: inherit;
      opacity: 0.85;
    }
    .footer-links a:hover {
      opacity: 1;
      text-decoration: underline;
    }
    /* Redes sociales */
    .social-links {
      display: flex;
      gap: 15px;
    }
    .social-links a {
      font-size: 1.5rem;
      color: inherit;
      opacity: 0.85;
      transition: transform 0.2s ease, opacity 0.2s ease;
    }
    .social-links a:hover {
      opacity: 1;
      transform: translateY(-3px);
    }
    /* Parte inferior */
    .footer-bottom {
      background: rgba(0,0,0,0.05);
      padding: 15px 0;
    }
    [data-bs-theme="dark"] .footer-bottom {
      background: rgba(255,255,255,0.05);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img id="logo" src="./../../CatInk_Proyecto/img/logo_alt.jpg" alt="CatInk Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
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
          <button id="themeToggle" class="btn btn-outline-secondary">
            <a href="#"><?php echo $fila['usuario'] ?></a>
          </button>
        </div>
      </div>
    </div>
  </div>
</nav>
