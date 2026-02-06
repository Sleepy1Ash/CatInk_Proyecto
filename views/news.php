<?php
include("./../layout/header.php");
include("./../data/conexion.php");
$id = intval($_GET['id'] ?? 1);
// Función para parsear categorías
function parseCategorias($s) {
    $s = trim($s ?? '');
    if ($s === '') return [];
    if (preg_match('/^\s*\[/', $s)) {
        $decoded = json_decode($s, true);
        if (is_array($decoded)) return array_filter(array_map('trim', $decoded));
    }
    $s = preg_replace('/[\\[\\]\\"\\\']/', '', $s);
    $parts = preg_split('/[,;|\/]+/', $s, -1, PREG_SPLIT_NO_EMPTY);
    return array_filter(array_map('trim', $parts));
}
// Obtener noticia
$sql = "SELECT * FROM noticias WHERE id = ? AND fecha_publicacion <= NOW()";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();
if (!$noticia) die("Noticia no encontrada");
// Obtener autor
$autor_nombre = "Desconocido";
if (!empty($noticia['autor'])) {
    $sql2 = "SELECT nombre FROM usuarios WHERE id_u = ?";
    $stmt2 = $con->prepare($sql2);
    $stmt2->bind_param("i", $noticia['autor']);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $autor_row = $res2->fetch_assoc();
    if ($autor_row) $autor_nombre = $autor_row['nombre'];
}
// Últimas y populares
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
?>
<div class="container mt-5">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-8">
        <div class="container-noticia">
          <?php foreach (parseCategorias($noticia['categoria']) as $cat): ?>
            <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
          <?php endforeach; ?>
          <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
          <p class="descripcion"><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>
          <p class="meta">
            Por <strong><?= htmlspecialchars($autor_nombre) ?></strong> —
            <?= date("d/m/Y H:i", strtotime($noticia['fecha_publicacion'])) ?>
          </p>
          <button id="likeBtn" class="like-btn" data-id="<?= $id ?>">
            ❤️ Like <span id="likeCount"><?= $noticia['likes'] ?></span>
          </button>
          <?php
            $img = !empty($noticia['crop1']) ? "./../" . htmlspecialchars($noticia['crop1']) : "./../img/placeholder.jpg";
          ?>
          <img src="<?= $img ?>" alt="" class="img-titular">
          <!-- Contenido completo de la noticia (sin inicializar Quill) -->
          <div class="ql-editor">
            <?= $noticia['contenido'] ?>
          </div>
          <button style="margin: 10px;">
            <a href="https://www.instagram.com/catink_/" target="_blank">
              <img src="./../img/publicidad2.jpeg" alt="" class="banner">
            </a>
          </button>
        </div>
      </div>
      <!-- SIDEBAR -->
      <div class="col-md-4">
        <div class="sidebar-wrapper">
          <div class="card sidebar-card">
            <button>
              <img src="./../img/publicidad.jpeg" class="card-img-top">
            </button>
            <div class="card-body">
              <h5>🆕 Lo más nuevo</h5>
              <ul class="list-group list-group-flush mb-3">
                <?php while ($row = $ultimas->fetch_assoc()): ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
              <h5>🔥 Lo más popular</h5>
              <ul class="list-group list-group-flush">
                <?php while ($row = $populares->fetch_assoc()): ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>">
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
<!-- Scripts de interacciones -->
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
<?php include("./../layout/footer.php"); ?>