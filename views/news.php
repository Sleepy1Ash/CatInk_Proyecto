<?php
include("./../layout/header.php");
include("./../data/conexion.php");
include("./helpers/videoEmbed.php");
$id = intval($_GET['id'] ?? 1);
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
<div class="container mt-5">
  <div class="container-fluid">
    <div class="row">
      <!-- COLUMNA PRINCIPAL -->
      <div class="col-md-8">
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
          <div class="ql-editor">
            <?= bloquearEmbeds($noticia['contenido']) ?>
          </div>
          <?php if ($secciones['publicidad']['estado'] == 1) : ?>
              <a href="<?= $publicidad['url'] ?>" class="banner-button" data-pub="<?= $publicidad['id_pub'] ?>">
                <img src="./../<?= $publicidad['imagen'] ?>" alt="" class="banner">
              </a>
          <?php endif; ?>
          <div class="share-wrapper">
            <div class="menus" id="shareMenu">
              <div class="toggles" onclick="toggleShareMenu()">
                <i class="bi bi-share-fill"></i>
              </div>
              <li style="--i: 0; --clr: var(--facebook)">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>" target="_blank">
                  <i class="bi bi-facebook"></i>
                </a>
              </li>
              <li style="--i: 1; --clr: var(--instagram)">
                <a href="https://wa.me/?text=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>" target="_blank">
                  <i class="bi bi-instagram"></i>
                </a>
              </li>
              <li style="--i: 2; --clr: var(--whatsapp)">
                <a href="https://wa.me/?text=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>" target="_blank">
                  <i class="bi bi-whatsapp"></i>
                </a>
              </li>
              <li style="--i: 3; --clr: var(--twitter)">
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>" target="_blank">
                  <i class="bi bi-twitter-x"></i>
                </a>
              </li>
              <li style="--i: 4; --clr: var(--linkedin)">
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>" target="_blank">
                  <i class="bi bi-linkedin"></i>
                </a>
              </li>
              <li style="--i: 5; --clr: var(--messenger)">
                <a href="https://www.facebook.com/dialog/send?link=<?= urlencode("https://tusitio.com/views/news.php?id=$id") ?>&app_id=TU_APP_ID" target="_blank">
                  <i class="bi bi-messenger"></i>
                </a>
              </li>
            </div>
          </div>
        </div>
      </div>
      <!-- SIDEBAR -->
      <div class="col-md-4">
        <div class="sidebar-wrapper">
          <div class="card sidebar-card">
            <?php if($secciones['publicidad']['estado'] == 1) : ?>
                <a href="<?= $publicidadCuadro['url'] ?>" class="banner-button" data-pub="<?= $publicidadCuadro['id_pub'] ?>">
                  <img src="./../<?= $publicidadCuadro['imagen'] ?>" class="card-img-top">
                </a>
            <?php endif; ?>
            <div class="card-body">
              <h3><i class="bi bi-alarm"></i> Lo más nuevo</h3>
              <hr>
              <ul class="list-group list-group-flush mb-3">
                <?php while ($row = $ultimas->fetch_assoc()): ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>" class="news-link">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
              <h3><i class="bi bi-fire"></i> Lo más popular</h3>
              <hr>
              <ul class="list-group list-group-flush">
                <?php while ($row = $populares->fetch_assoc()): ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>" class="news-link">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
            </div>
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
  function toggleShareMenu(){
    document.getElementById("shareMenu").classList.toggle("active");
  }
</script>
<?php include("./../layout/footer.php"); ?>