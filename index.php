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
        n.fecha_publicacion as fecha,
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
$noticiasMasRecientes = array_slice($noticias, 7, 11);
//Obtener banner publicidad
$stmt = $con->prepare("SELECT * FROM publicidad WHERE activo = 1 AND tipo = 1 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidad = $stmt->get_result()->fetch_assoc();
//Obtener cuadro publicitario
$stmt = $con->prepare("SELECT * FROM publicidad WHERE activo = 1 AND tipo = 2 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidadCuadro = $stmt->get_result()->fetch_assoc();
//obtener publicidad inferior
$stmt = $con->prepare("SELECT * FROM publicidad WHERE activo = 1 AND tipo = 1 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidadInferior = $stmt->get_result()->fetch_assoc();
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
        <center><h2>Últimas Noticias de la Semana</h2><br></center>
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
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[0]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[0]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[1]['crop3'] ?? $ultimasNoticias[1]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[1]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[1]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[1]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[1]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[2]['crop3'] ?? $ultimasNoticias[2]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[2]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[2]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[2]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[2]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[3]['crop3'] ?? $ultimasNoticias[3]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[3]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[3]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[3]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[3]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[4]['crop3'] ?? $ultimasNoticias[4]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[4]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[4]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[4]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[4]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[5]['crop3'] ?? $ultimasNoticias[5]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[5]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[5]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[5]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[5]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="news-card">
                    <img src="<?= htmlspecialchars($ultimasNoticias[6]['crop2'] ?? $ultimasNoticias[6]['crop1'] ?? 'img/placeholder.jpg') ?>" alt="">
                    <div class="news-overlay">
                        <?php foreach(array_filter(array_map('trim', explode(',', $ultimasNoticias[6]['categorias'] ?? ''))) as $cat): ?>
                            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                        <a href="./views/news.php?id=<?= $ultimasNoticias[6]['id'] ?>" class="news-link">
                            <h3 class="title-limit-2"><?= htmlspecialchars($ultimasNoticias[6]['titulo']) ?></h3>
                        </a>
                        <p class="description-limit-1"><?= htmlspecialchars($ultimasNoticias[6]['descripcion']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- ===================== -->
        <!-- SIDEBAR -->
        <!-- ===================== -->
        <div class="row mt-5">
            <div class="col-md-8">
                <a href="<?php echo htmlspecialchars($publicidad['url']); ?>" class="banner-button" data-pub="<?php echo htmlspecialchars($publicidad['id_pub']); ?>">
                    <img src="<?php echo htmlspecialchars($publicidad['imagen']); ?>" alt="" class="banner">
                </a>
                <center>
                    <h2>Noticias mas recientes</h2>
                </center>
                <?php foreach($noticiasMasRecientes as $row): ?>
                    <div class="card mb-3">
                        <div class="row row-no-gap">
                            <div class="col-md-4">
                                <img src="<?= htmlspecialchars($row['crop3']  ?? 'img/placeholder.jpg') ?>" alt="" class="card-img-left">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <?php foreach(array_filter(array_map('trim', explode(',', $row['categorias'] ?? ''))) as $cat): ?>
                                        <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                                    <?php endforeach; ?>
                                    <h5 class="card-title">
                                        <a href="./views/news.php?id=<?= $row['id'] ?>" class="news-link"><?= htmlspecialchars($row['titulo']) ?></a>
                                    </h5>
                                    <p><?= htmlspecialchars($row['descripcion']) ?></p>
                                    <small class="text-muted">Publicado: <?= date("M d", strtotime($row['fecha'])) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <a href="<?php echo htmlspecialchars($publicidadInferior['url']); ?>" class="banner-button" data-pub="<?php echo htmlspecialchars($publicidadInferior['id_pub']); ?>">
                    <img src="<?php echo htmlspecialchars($publicidadInferior['imagen']); ?>" alt="" class="banner">
                </a>
            </div>
            <div class="col-md-4">
                <div class="sidebar-wrapper">
                    <div class="card sidebar-card">
                        <a href="<?php echo htmlspecialchars($publicidadCuadro['url']); ?>" class="banner-button" data-pub="<?php echo htmlspecialchars($publicidadCuadro['id_pub']); ?>">
                            <img src="<?php echo htmlspecialchars($publicidadCuadro['imagen']); ?>" class="card-img-top">
                        </a>
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
<!-- Conteo de clicks -->
<script>
    document.querySelectorAll(".banner-button").forEach(banner => {
        banner.addEventListener("click", function(e) {
            e.preventDefault();//pausar redireccionamiento
            let url = this.href;
            let publicidadId = this.dataset.pub;
            let data = new FormData();
            data.append("publicidad_id", publicidadId);
            fetch("./controllers/publicidad_click.php", {
                method: "POST",
                body: data
            }).finally(()=>{
                window.location.href = url;//redireccionar despues de registrar click
            });
        });
    });
</script>
<!-- Conteo de tiempo y visualizaciones -->
<script>
    document.querySelectorAll(".banner-button").forEach(banner => {
        let publicidadId = banner.dataset.pub;
        let startTime = null;
        let totalTime = 0;
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    startTime = Date.now();
                } else if (startTime) {
                    totalTime += (Date.now() - startTime) / 1000;
                    startTime = null;
                }
            });
        }, { threshold: 0.5 });
        observer.observe(banner);
        setInterval(()=>{
            if (totalTime > 1) {
                let data = new FormData();
                data.append("publicidad_id", publicidadId);
                data.append("tiempo", Math.round(totalTime));
                navigator.sendBeacon("./controllers/publicidad_view.php", data);
                totalTime = 0;  
            }
        }, 5000);
    });
</script>
<?php include(__DIR__ . "/layout/footer.php"); ?>