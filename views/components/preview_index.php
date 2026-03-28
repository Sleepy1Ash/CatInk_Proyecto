<?php
// Datos simulados (o pasados dinámicamente)
$row = [
    "id" => 0,
    "titulo" => $titulo ?? "Título de ejemplo",
    "descripcion" => $descripcion ?? "Descripción de ejemplo",
    "categorias" => $categorias ?? "Demo",
    "crop1" => $preview1 ?? "img/placeholder.jpg",
    "crop2" => $preview2 ?? "img/placeholder.jpg",
    "crop3" => $preview3 ?? "img/placeholder.jpg"
];
?>

<!-- ===================== -->
<!-- PREVIEW CARRUSEL -->
<!-- ===================== -->
<div class="preview-block">
    <h5>Vista en carrusel</h5>

    <div class="carousel-item active">
        <picture>
            <source media="(max-width:768px)" srcset="<?= $row['crop1'] ?>">
            <img id="preview-carousel" src="<?= $preview2 ?? 'img/placeholder.jpg' ?>" class="carousel-img">
        </picture>

        <div class="carousel-caption caption-md">
            <?php foreach(explode(',', $row['categorias']) as $cat): ?>
                <span class="carousel-tag"><?= htmlspecialchars(trim($cat)) ?></span>
            <?php endforeach; ?>

            <h5><?= htmlspecialchars($row['titulo']) ?></h5>
            <p><?= htmlspecialchars($row['descripcion']) ?></p>
        </div>
    </div>
</div>

<!-- ===================== -->
<!-- PREVIEW NEWS CARD -->
<!-- ===================== -->
<div class="preview-block mt-4">
    <h5>Vista en tarjetas</h5>

    <div class="news-card">
        <img id="preview-card" src="<?= $preview3 ?? 'img/placeholder.jpg' ?>">

        <div class="news-overlay">
            <div class="news-tags">
                <?php foreach(explode(',', $row['categorias']) as $cat): ?>
                    <span class="news-tag"><?= htmlspecialchars(trim($cat)) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="news-content">
                <h3><?= htmlspecialchars($row['titulo']) ?></h3>
                <p><?= htmlspecialchars($row['descripcion']) ?></p>
            </div>
        </div>
    </div>
</div>