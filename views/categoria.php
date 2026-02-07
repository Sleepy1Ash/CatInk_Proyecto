<?php
include("./../layout/header.php");
include("./../data/conexion.php");
$q         = trim($_GET['q'] ?? '');
$categoria = trim($_GET['cat'] ?? '');
// ==============================
// CONSULTA PRINCIPAL DINÁMICA
// ==============================
if ($q !== '') {
    $stmt = $con->prepare("
        SELECT n.id, n.titulo, n.descripcion, n.crop3, n.fecha_publicacion,
               GROUP_CONCAT(c.nombre SEPARATOR ',') AS categorias
        FROM noticias n
        LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
        LEFT JOIN categorias c ON nc.categoria_id = c.id_c
        WHERE n.fecha_publicacion <= NOW()
          AND (n.titulo LIKE ? OR n.descripcion LIKE ? OR n.contenido LIKE ?)
        GROUP BY n.id
        ORDER BY n.fecha_publicacion DESC
    ");
    $like = "%$q%";
    $stmt->bind_param("sss", $like, $like, $like);
} elseif ($categoria !== '') {
    $stmt = $con->prepare("
        SELECT n.id, n.titulo, n.descripcion, n.crop3, n.fecha_publicacion,
              GROUP_CONCAT(c.nombre SEPARATOR ',') AS categorias
        FROM noticias n
        INNER JOIN noticia_categoria nc_filter ON n.id = nc_filter.noticia_id
        INNER JOIN categorias c_filter ON nc_filter.categoria_id = c_filter.id_c AND c_filter.nombre = ?
        LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
        LEFT JOIN categorias c ON nc.categoria_id = c.id_c
        WHERE n.fecha_publicacion <= NOW()
        GROUP BY n.id
        ORDER BY n.fecha_publicacion DESC
    ");
    $stmt->bind_param("s", $categoria);
} else {
    $stmt = $con->prepare("
        SELECT n.id, n.titulo, n.descripcion, n.crop3, n.fecha_publicacion,
               GROUP_CONCAT(c.nombre SEPARATOR ',') AS categorias
        FROM noticias n
        LEFT JOIN noticia_categoria nc ON n.id = nc.noticia_id
        LEFT JOIN categorias c ON nc.categoria_id = c.id_c
        WHERE n.fecha_publicacion <= NOW()
        GROUP BY n.id
        ORDER BY n.fecha_publicacion DESC
        LIMIT 20
    ");
}
$stmt->execute();
$result = $stmt->get_result();
// ==============================
// SIDEBAR
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
?>
<div class="container mt-5">
  <div class="container-fluid">
    <!-- TITULO CONTEXTUAL -->
    <?php if ($q !== ''): ?>
      <h4 style="margin:15px 0">
        Resultados para: <strong><?= htmlspecialchars($q) ?></strong>
      </h4>
    <?php elseif ($categoria !== ''): ?>
      <h4 style="margin:15px 0">
        Categoría: <strong><?= htmlspecialchars($categoria) ?></strong>
      </h4>
    <?php endif; ?>
    <div class="row">
      <!-- ================== COLUMNA PRINCIPAL ================== -->
      <div class="col-md-8">
        <?php if ($result->num_rows === 0): ?>
          <p>No se encontraron resultados.</p>
        <?php endif; ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
            $cats = !empty($row['categorias']) ? explode(",", $row['categorias']) : [];
            // LIMPIAR ESPACIOS
            $cats = array_map('trim', $cats);
            // SI HAY FILTRO POR CATEGORÍA, PONERLA PRIMERO
            if ($categoria !== '' && in_array($categoria, $cats)) {
                // Quitarla del array
                $cats = array_diff($cats, [$categoria]);
                // Volver a ponerla al inicio
                array_unshift($cats, $categoria);
            }
            $img = !empty($row['crop3']) ? "./../".$row['crop3'] : "./../img/placeholder.jpg";
          ?>
          <div class="card mb-3">
            <div class="row row-no-gap">
              <div class="col-md-4">
                <img src="<?= htmlspecialchars($img) ?>" class="card-img-left">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <!-- CATEGORÍAS -->
                  <?php foreach ($cats as $cat): ?>
                    <span class="news-tag"><?= htmlspecialchars(trim($cat)) ?></span>
                  <?php endforeach; ?>
                  <h5 class="card-title">
                    <a href="./../views/news.php?id=<?= $row['id'] ?>" class="news-link">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </h5>
                  <p><?= htmlspecialchars($row['descripcion']) ?></p>
                  <small class="text-muted">
                    <?= date('d M Y', strtotime($row['fecha_publicacion'])) ?>
                  </small>
                </div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
      <!-- ================== SIDEBAR ================== -->
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
                    <a href="./../views/news.php?id=<?= $row['id'] ?>">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </li>
                <?php endwhile; ?>
              </ul>
              <h5>🔥 Lo más popular</h5>
              <ul class="list-group list-group-flush">
                <?php while ($row = $populares->fetch_assoc()): ?>
                  <li class="list-group-item">
                    <a href="./../views/news.php?id=<?= $row['id'] ?>">
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
<?php include("./../layout/footer.php"); ?>