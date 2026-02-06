<?php
include(__DIR__ . "/layout/header.php");
include(__DIR__ . "/data/conexion.php");
// =====================
// Obtener todas las noticias con sus categorías
// =====================
$stmt = $con->prepare("
    SELECT 
        n.id,
        n.titulo,
        n.descripcion,
        n.crop1,
        n.crop2,
        n.crop3,
        n.fecha_publicacion,
        n.likes,
        GROUP_CONCAT(c.nombre SEPARATOR ',') AS categorias
    FROM noticias n
    LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
    LEFT JOIN categorias c ON nc.categoria_id = c.id_c
    WHERE n.fecha_publicacion <= NOW()
    GROUP BY n.id
    ORDER BY n.fecha_publicacion DESC
");
$stmt->execute();
$result = $stmt->get_result();
$noticias = $result->fetch_all(MYSQLI_ASSOC);
// Últimas 3 noticias para sidebar
$ultimasNoticiasSidebar = array_slice($noticias, 0, 3);
// Noticias más populares (por likes)
$popularesNoticiasSidebar = $noticias;
usort($popularesNoticiasSidebar, fn($a,$b)=>$b['likes']-$a['likes']);
$popularesNoticiasSidebar = array_slice($popularesNoticiasSidebar, 0, 3);
// Noticias principales para slider y últimas
$slider = array_slice($noticias, 0, 5);
$ultimasNoticias = array_slice($noticias, 0, 7);
?>
<!-- ===================== -->
<!-- SLIDER PRINCIPAL -->
<!-- ===================== -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
    <div class="carousel-indicators custom-indicators">
        <?php foreach($slider as $i => $row): ?>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?= $i ?>" class="<?= $i==0?'active':'' ?>">
                <div class="indicator-avatar">
                    <img src="./<?= htmlspecialchars($row['crop1'] ?? 'img/placeholder.jpg') ?>" alt="<?= htmlspecialchars($row['titulo']) ?>">
                    <svg viewBox="0 0 36 36"><circle cx="18" cy="18" r="16"></circle></svg>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach($slider as $i => $row): ?>
            <div class="carousel-item <?= $i==0?'active':'' ?>">
                <img src="./<?= htmlspecialchars($row['crop2'] ?? $row['crop1'] ?? 'img/placeholder.jpg') ?>" class="carousel-img">
                <div class="carousel-caption caption-md">
                    <?php foreach(array_filter(array_map('trim', explode(',', $row['categorias'] ?? ''))) as $cat): ?>
                        <span class="carousel-tag"><?= htmlspecialchars($cat) ?></span>
                    <?php endforeach; ?>
                    <h5><a href="./views/news.php?id=<?= $row['id'] ?>" class="carousel-link"><?= htmlspecialchars($row['titulo']) ?></a></h5>
                    <p><?= htmlspecialchars($row['descripcion']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- ===================== -->
<!-- ÚLTIMAS NOTICIAS -->
<!-- ===================== -->
<div class="container mt-5">
    <div class="container-fluid">
        <center><h2>Últimas Noticias</h2><br></center>
        <div class="row">
            <!-- Primeras 2 noticias principales -->
            <div class="col-md-8">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[0]['crop2'] ?? $ultimasNoticias[0]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[0]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[0]['id'] ?>" class="news-link">
                            <h3><?= htmlspecialchars($ultimasNoticias[0]['titulo']) ?></h3>
                        </a>
                        <p><?= htmlspecialchars($ultimasNoticias[0]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[1]['crop2'] ?? $ultimasNoticias[1]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[1]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[1]['id'] ?>" class="news-link">
                            <h3><?= htmlspecialchars($ultimasNoticias[1]['titulo']) ?></h3>
                        </a>
                        <p><?= htmlspecialchars($ultimasNoticias[1]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Noticias restantes -->
        <div class="row mt-3">
            <?php for($i=2; $i < count($ultimasNoticias); $i++): ?>
                <div class="col-md-<?= ($i==6)?'8':'4' ?> mb-3">
                    <div class="news-card">
                        <img src="<?= htmlspecialchars($ultimasNoticias[$i]['crop3'] ?? $ultimasNoticias[$i]['crop2'] ?? 'img/placeholder.jpg') ?>" alt="">
                        <div class="news-overlay">
                            <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[$i]['categorias'] ?? ''))) as $cat): ?>
                                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                            <a href="./views/news.php?id=<?= $ultimasNoticias[$i]['id'] ?>" class="news-link">
                                <h3><?= htmlspecialchars($ultimasNoticias[$i]['titulo']) ?></h3>
                            </a>
                            <p><?= htmlspecialchars($ultimasNoticias[$i]['descripcion']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <!-- ===================== -->
        <!-- SIDEBAR -->
        <!-- ===================== -->
        <div class="row mt-5">
            <div class="col-md-8">
                <!-- Aquí puedes agregar banners y secciones de noticias adicionales -->
            </div>
            <div class="col-md-4">
                <div class="sidebar-wrapper">
                    <div class="card sidebar-card">
                        <img src="img/publicidad.jpeg" class="card-img-top">
                        <div class="card-body">
                            <h5>🆕 Lo más nuevo</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <?php foreach($ultimasNoticiasSidebar as $row): ?>
                                    <li class="list-group-item">
                                        <a href="./views/news.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['titulo']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <h5>🔥 Lo más popular</h5>
                            <ul class="list-group list-group-flush">
                                <?php foreach($popularesNoticiasSidebar as $row): ?>
                                    <li class="list-group-item">
                                        <a href="./views/news.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['titulo']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include(__DIR__ . "/layout/footer.php"); ?>
