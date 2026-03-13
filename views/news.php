<?php
include("./../layout/header.php");
include("./../data/conexion.php");
include("./helpers/videoEmbed.php");
include("./helpers/socialEmbed.php");
if(isset($_GET['hash'])){
    $id = decodeId($_GET['hash']);
}else{
    $id = intval($_GET['id'] ?? 1);
}
// ==============================
// Obtener noticia con autor y categorías
// ==============================
$sql = "
    SELECT n.*, u.nombre AS autor_nombre,
           GROUP_CONCAT(c.nombre SEPARATOR ',') AS categorias
    FROM noticias n
    LEFT JOIN usuarios u ON n.autor = u.id_u
    LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
    LEFT JOIN categorias c ON nc.categoria_id = c.id_c
    WHERE n.id = ? AND n.fecha_publicacion <= NOW()
    GROUP BY n.id
";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();
if (!$noticia) die("Noticia no encontrada");
// Parsear categorías
$cats = !empty($noticia['categorias']) ? explode(',', $noticia['categorias']) : [];
$cats = array_map('trim', $cats);
// ==============================
// NOTICIAS RECOMENDADAS
// ==============================
$recomendadas = [];
if(!empty($cats)){
    $placeholders = implode(',', array_fill(0, count($cats), '?'));
    $sqlRec = "
        SELECT DISTINCT n.id, n.titulo, n.crop3, n.fecha_publicacion
        FROM noticias n
        JOIN noticia_categoria nc ON n.id = nc.noticia_id
        JOIN categorias c ON nc.categoria_id = c.id_c
        WHERE c.nombre IN ($placeholders)
        AND n.id != ?
        AND n.fecha_publicacion <= NOW()
        ORDER BY n.fecha_publicacion DESC
        LIMIT 3
    ";
    $stmtRec = $con->prepare($sqlRec);
    $types = str_repeat("s", count($cats)) . "i";
    $params = array_merge($cats, [$id]);
    $stmtRec->bind_param($types, ...$params);
    $stmtRec->execute();
    $recomendadas = $stmtRec->get_result();
}
// ==============================
// NOTICIAS RECIENTES
// ==============================
$stmtRecientes = $con->prepare("
    SELECT id, titulo, crop3, fecha_publicacion
    FROM noticias
    WHERE fecha_publicacion <= NOW()
    AND id != ?
    ORDER BY fecha_publicacion DESC
    LIMIT 4
");
$stmtRecientes->bind_param("i", $id);
$stmtRecientes->execute();
$recientes = $stmtRecientes->get_result();
// ==============================
// Últimas y Populares
// ==============================
$stmtUltimas = $con->prepare("
    SELECT id, titulo
    FROM noticias
    WHERE fecha_publicacion <= NOW()
    ORDER BY fecha_publicacion DESC
    LIMIT 3
");
$stmtUltimas->execute();
$ultimas = $stmtUltimas->get_result();
$stmtPopulares = $con->prepare("
    SELECT id, titulo
    FROM noticias
    ORDER BY likes DESC
    LIMIT 3
");
$stmtPopulares->execute();
$populares = $stmtPopulares->get_result();
//Obtener banner publicidad
$stmt = $con->prepare("SELECT * FROM publicidad WHERE activo = 1 AND tipo = 1 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidad = $stmt->get_result()->fetch_assoc();
//Obtener cuadro publicitario
$stmt = $con->prepare("SELECT * FROM publicidad WHERE activo = 1 AND tipo = 2 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$publicidadCuadro = $stmt->get_result()->fetch_assoc();
?>
<style>
  @media (max-width: 768px) {
    /* Eliminar padding de contenedores */
    .noticias > .container {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    .container > .container-fluid, .container-noticia {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
  }
</style>
<div class="noticias">
  <div class="container">
    <div class="container-fluid">
      <div class="row">
        <!-- COLUMNA PRINCIPAL -->
        <div class="col-md-9">
          <div class="container-noticia">
            <?php
              $img = !empty($noticia['crop1']) ? "./../" . htmlspecialchars($noticia['crop1']) : "./../img/placeholder.jpg";
            ?>
            <img src="<?= $img ?>" alt="" class="img-titular">
            <!-- Categorías -->
            <?php foreach ($cats as $cat): ?>
              <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
            <?php endforeach; ?>
            <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
            <p class="descripcion"><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>
            <p class="meta">
              Por <strong><?= htmlspecialchars($noticia['autor_nombre'] ?? 'Desconocido') ?></strong> —
              <?= date("d/m/Y H:i", strtotime($noticia['fecha_publicacion'])) ?>
            </p>
            <button id="likeBtn" class="like-btn" data-id="<?= $id ?>">
              ❤️ Like <span id="likeCount"><?= $noticia['likes'] ?></span>
            </button>
            <!-- Contenido completo de la noticia -->
            <div class="post-content">
              <?php
                $contenido=$noticia['contenido'];
                $contenido=procesarEmbedsSociales($contenido);
                $contenido=bloquearEmbeds($contenido);
                echo $contenido
              ?>
            </div>
            <div class="share-bar">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://www.catink.com.mx/".newsUrl($id)) ?>" target="_blank" class="share-btn facebook">
                    <i class="bi bi-facebook"></i>
                </a>
                <!--<a href="#" onclick="copyLink()" class="share-btn instagram">
                    <i class="bi bi-instagram"></i>
                </a>-->
                <a href="https://wa.me/?text=<?= urlencode("https://www.catink.com.mx/".newsUrl($id)) ?>" target="_blank" class="share-btn whatsapp">
                    <i class="bi bi-whatsapp"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode("https://www.catink.com.mx/".newsUrl($id)) ?>" target="_blank" class="share-btn twitter">
                    <i class="bi bi-twitter-x"></i>
                </a>
                <!--<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode("https://www.catink.com.mx/".newsUrl($id)) ?>" target="_blank" class="share-btn linkedin">
                    <i class="bi bi-linkedin"></i>
                </a>-->
                <a href="#" onclick="shareMessenger()" class="share-btn messenger">
                    <i class="bi bi-messenger"></i>
                </a>
            </div>
            <?php if ($secciones['publicidad']['estado'] == 1) : ?>
                <a href="<?= $publicidad['url'] ?>" class="banner-button" data-pub="<?= $publicidad['id_pub'] ?>">
                  <img src="./../<?= $publicidad['imagen'] ?>" alt="" class="banner">
                </a>
            <?php endif; ?>
          </div>
        </div>
        <!-- SIDEBAR -->
        <div class="col-md-3">
          <div class="sidebar-wrapper">
            <div class="card sidebar-card">
              <?php if($secciones['publicidad']['estado'] == 1) : ?>
                  <a href="<?= $publicidadCuadro['url'] ?>" class="banner-button" data-pub="<?= $publicidadCuadro['id_pub'] ?>">
                    <img src="./../<?= $publicidadCuadro['imagen'] ?>" class="banner-card-img-top">
                  </a>
              <?php endif; ?>
              <div class="card-body">
                <h3><i class="bi bi-alarm"></i> Lo más nuevo</h3>
                <ul class="list-group list-group-flush mb-3">
                  <?php while ($row = $ultimas->fetch_assoc()): ?>
                    <li class="list-group-item">
                      <a href="<?= newsUrl($row['id']) ?>" class="news-link">
                        <i class="bi bi-file-earmark-richtext"></i> <?= htmlspecialchars($row['titulo']) ?>
                      </a>
                    </li>
                  <?php endwhile; ?>
                </ul>
                <h3><i class="bi bi-fire"></i> Lo más popular</h3>
                <ul class="list-group list-group-flush">
                  <?php while ($row = $populares->fetch_assoc()): ?>
                    <li class="list-group-item">
                      <a href="<?= newsUrl($row['id']) ?>" class="news-link">
                        <i class="bi bi-file-earmark-richtext"></i> <?= htmlspecialchars($row['titulo']) ?>
                      </a>
                    </li>
                  <?php endwhile; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="container">
          <h3>Recomendados para ti</h3>
          <hr>
          <div class="row">
            <?php while($r = $recomendadas->fetch_assoc()): 
                $img = !empty($r['crop3']) ? "./../".$r['crop3'] : "./../img/placeholder.jpg";
            ?>
              <div class="col">
                  <div class="card h-100">
                      <img src="<?= htmlspecialchars($img) ?>" class="card-img-top">
                      <div class="card-body">
                          <h6>
                              <a href="<?= newsUrl($r['id']) ?>" class="news-link">
                                  <?= htmlspecialchars($r['titulo']) ?>
                              </a>
                          </h6>
                          <small class="text-muted">
                              <?= date('d M Y', strtotime($r['fecha_publicacion'])) ?>
                          </small>
                      </div>
                  </div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
      <br>
      <div class="row">
        <div class="container">
          <h3>Noticias recientes</h3>
          <hr>
          <div class="row">
            <?php while($r = $recientes->fetch_assoc()): 
                $img = !empty($r['crop3']) ? "./../".$r['crop3'] : "./../img/placeholder.jpg";
            ?>
              <div class="col">
                  <div class="card h-100">
                      <img src="<?= htmlspecialchars($img) ?>" class="card-img-top">
                      <div class="card-body">
                          <h6>
                              <a href="<?= newsUrl($r['id']) ?>" class="news-link">
                                  <?= htmlspecialchars($r['titulo']) ?>
                              </a>
                          </h6>
                          <small class="text-muted">
                              <?= date('d M Y', strtotime($r['fecha_publicacion'])) ?>
                          </small>
                      </div>
                  </div>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Scripts de interacción -->
<script>
  // Sumar vistas
  fetch("./../controllers/sumarvistas.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "noticia_id=<?= $id ?>"
  })
  .then(res => res.json())
  .then(data => console.log("Vistas actualizadas:", data))
  .catch(err => console.error(err));
  // Enviar tiempo de lectura
  let inicio = Date.now();
  let enviado = false;
  function enviarTiempo() {
    if (enviado) return;
    enviado = true;
    let tiempo = Math.floor((Date.now() - inicio) / 1000);
    navigator.sendBeacon("./../controllers/guardartiempo.php",
      new URLSearchParams({ noticia_id: "<?= $id ?>", tiempo: tiempo })
    );
  }
  window.addEventListener("beforeunload", enviarTiempo);
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") enviarTiempo();
  });
  // Botón de Like
  document.getElementById('likeBtn').addEventListener('click', async function() {
    const id = this.dataset.id;
    const res = await fetch('./../controllers/like.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `noticia_id=${id}`
    });
    const data = await res.json();
    if (data.ok) {
      const count = document.getElementById('likeCount');
      count.textContent = parseInt(count.textContent) + 1;
      this.disabled = true;
    } else {
      alert(data.msg);
      this.disabled = true;
    }
  });
</script>
<script>
  function copyLink(){
    const link = "https://www.catink.com.mx/<?= newsUrl($id) ?>";
    navigator.clipboard.writeText(link);
    window.open("https://www.instagram.com/direct/inbox/", "_blank");
  }
  function shareMessenger() {
    const url = "https://www.catink.com.mx/<?= newsUrl($id) ?>";
    const encoded = encodeURIComponent(url);
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if(isMobile){
        // abre messenger en móvil
        window.open("fb-messenger://share/?link=" + encoded, "_blank");
    }else{
        // abre diálogo web en PC
        window.open(
            "https://www.facebook.com/dialog/send?link=" + encoded + "&app_id=TU_APP_ID&redirect_uri=" + encoded,
            "_blank"
        );
    }
}
</script>
<?php include("./../layout/footer.php"); ?>