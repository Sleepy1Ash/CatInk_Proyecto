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
    .carousel,
    .carousel-inner,
    .carousel-item {
        max-height: 45vh;
    }
    .carousel-item{
        position: relative;
    }
    .carousel-item img {
        height: 45vh;
        object-fit: cover;
    }
    .carousel-item::before {
      content: "";
      position: absolute;
      inset: 0;
      z-index: 1;
      background: linear-gradient(
        to right,
        rgba(0,0,0,0.85) 0%,
        rgba(0,0,0,0.55) 40%,
        rgba(0,0,0,0.25) 65%,
        rgba(0,0,0,0.05) 80%,
        rgba(0,0,0,0) 100%
      );
    }
    .carousel-caption {
      z-index: 2;
    }
    :root{
        --slide-duration: 5s;
    }
    .carousel-indicators,
    .carousel-indicators [data-bs-target],
    .carousel-indicators button {
        all:unset;
        cursor: pointer;
    }
    .carousel-indicators .active::before {
        animation: slideProgress 5s linear forwards;
    }
    .custom-indicators {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 12px;
      z-index: 10;
      justify-content: flex-start;
      padding-left: 6%;
    }
    .custom-indicators button {
      background: none;
      border: none;
      padding: 0;
    }
    .indicator-avatar {
      position: relative;
      width: 72px;
      height: 72px;
    }
    .indicator-avatar img {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      object-fit: cover;
    }
    .indicator-avatar svg {
      position: absolute;
      inset: 0;
      transform: rotate(-90deg);
    }
    .indicator-avatar circle {
      fill: none;
      stroke: #fff;
      stroke-width: 3;
      stroke-dasharray: 100;
      stroke-dashoffset: 100;
      transition: stroke-dashoffset linear;
    }
    [data-bs-theme="dark"] .indicator-avatar circle {
      stroke: #fff;
    }
    [data-bs-theme="light"] .indicator-avatar circle {
      stroke: #000;
    }
    .carousel-caption {
      position: absolute;
      inset: 0;
      z-index: 2;

      display: flex;
      flex-direction: column;
      justify-content: center;

      padding-left: 6%;
      padding-right: 50%;
      text-align: left;
    }
    .carousel-caption h5 {
      font-size: 3rem;
      font-weight: 800;
      line-height: 1.2;
    }
    .carousel-caption p {
      font-size: 1.1rem;
      max-width: 600px;
      margin-top: 15px;
      opacity: 0.9;
    }
    .carousel-caption h5,
    .carousel-caption p {
      color: #fff;
      text-shadow: 0 4px 18px rgba(0,0,0,0.9);
    }
    .carousel-tag {
      text-transform: uppercase;
      font-size: 0.85rem;
      font-weight: 600;
      opacity: 0.8;
      margin-bottom: 10px;
      color: #EF3363;
    }
    @keyframes slideProgress {
        from { width: 0%; }
        to   { width: 100%; }
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
            <a href="views/login.php"><i class="bi bi-person-fill"></i></a>
          </button>
        </div>
      </div>
    </div>
  </div>
</nav>
