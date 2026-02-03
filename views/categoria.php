<?php
    include("./../layout/header.php");
    include("./../data/conexion.php");
    $categoria = $_GET['cat'];
    // todas las noticias
    $stmt = $con -> prepare("SELECT id, titulo, descripcion, categoria, crop1, crop2,
    crop3, fecha_publicacion FROM noticias WHERE fecha_publicacion<=NOW() AND categoria=? ORDER BY id DESC");
    $stmt -> bind_param("s", $categoria);
    $stmt -> execute();
    $result = $stmt -> get_result();
    // ultimas noticias
    $stmtUltimas = $con->prepare("
        SELECT id, titulo
        FROM noticias
        WHERE fecha_publicacion <= NOW()
        ORDER BY fecha_publicacion DESC
        LIMIT 3
    ");
    $stmtUltimas->execute();
    $ultimas = $stmtUltimas->get_result();
    // noticias populares
    $stmtPopulares = $con->prepare("
        SELECT id, titulo
        FROM noticias
        ORDER BY likes DESC
        LIMIT 3
    ");
    $stmtPopulares->execute();
    $populares = $stmtPopulares->get_result();
?>
<div class="container-fluid">
    <div class="row">
      <!-- COLUMNA PRINCIPAL -->
      <div class="col-md-8">
        <?php
          while ($row = $result->fetch_assoc()) {
        ?>
          <div class="card mb-3">
            <div class="row row-no-gap">

              <div class="col-md-4">
                <img src="./../<?= $row['crop3'] ?>" class="card-img-left">
              </div>

              <div class="col-md-8">
                <div class="card-body">
                  <span class="news-tag"><?= htmlspecialchars($row['categoria']) ?></span>

                  <h5 class="card-title">
                    <a href="./views/news.php?id=<?= htmlspecialchars($row['id']) ?>" class="news-link"><?= htmlspecialchars($row['titulo']) ?></a>
                  </h5>

                  <p><?= htmlspecialchars($row['descripcion']) ?></p> 
                  <small class="text-muted"><?= date('d M Y', strtotime($row['fecha_publicacion'])) ?></small>
                </div>
              </div>

            </div>
          </div>
        <?php
          }
        ?>
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
                <?php while ($row = $ultimas->fetch_assoc()) { ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>">
                      <?= $row['titulo'] ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>

              <h5>🔥 Lo más popular</h5>
              <ul class="list-group list-group-flush">
                <?php while ($row = $populares->fetch_assoc()) { ?>
                  <li class="list-group-item">
                    <a href="./views/news.php?id=<?= $row['id'] ?>">
                      <?= $row['titulo'] ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>

            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<?php
    include("./../layout/footer.php");
?>