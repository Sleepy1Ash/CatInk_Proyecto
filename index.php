<?php
    include("./layout/header.php");
    include("./data/conexion.php");
    // todas las noticias
    $stmt = $con -> prepare("SELECT id, titulo, descripcion, categoria, crop1, crop2,
    crop3, fecha_publicacion FROM noticias WHERE fecha_publicacion<=NOW() ORDER BY id DESC");
    $stmt -> execute();
    $result = $stmt -> get_result();
    $noticias = $result->fetch_all(MYSQLI_ASSOC);
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
<!-- CONTENIDO -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
  <?php
    $slider = array_slice($noticias, 0, 5);
  ?>
  <div class="carousel-indicators custom-indicators">
    <?php foreach($slider as $i => $row): ?>
      <button type="button"
        data-bs-target="#carouselExampleCaptions"
        data-bs-slide-to="<?= $i ?>"
        class="<?= $i==0?'active':'' ?>">

        <div class="indicator-avatar">
          <img src="./<?= $row['crop1'] ?>" alt="<?= htmlspecialchars($row['titulo']) ?>">
          <svg viewBox="0 0 36 36"><circle cx="18" cy="18" r="16"></circle></svg>
        </div>

      </button>
    <?php endforeach; ?>
  </div>
  <div class="carousel-inner">
    <?php foreach($slider as $i => $row): ?>
      <div class="carousel-item <?= $i==0?'active':'' ?>">
        <img src="./<?= $row['crop2'] ?>" class="carousel-img">

        <div class="carousel-caption caption-md">
          <span class="carousel-tag"><?= $row['categoria'] ?></span>

          <h5>
            <a href="./views/news.php?id=<?= $row['id'] ?>" class="carousel-link">
              <?= $row['titulo'] ?>
            </a>
          </h5>

          <p><?= $row['descripcion'] ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<div class="container mt-5">
  <div class="container-fluid">
    <?php
      $result->data_seek(0); // volver a empezar
      $row = $result->fetch_all(MYSQLI_ASSOC);
    ?>
    <div class="row">
      <div class="row">
        <div class="col-md-8">
          <div class="news-card">
            <img src="<?= $row[0]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[0]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[0]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[0]['titulo']) ?></h3>
              </a>
              
              <p><?= htmlspecialchars($row[0]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="news-card">
            <img src="<?= $row[1]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[1]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[1]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[1]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[1]['descripcion']) ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="news-card">
            <img src="<?= $row[2]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[2]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[2]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[2]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[2]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="news-card">
            <img src="<?= $row[3]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[3]['categoria']) ?></span> 
              <a href="./views/news.php?id=<?= htmlspecialchars($row[3]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[3]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[3]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="news-card">
            <img src="<?= $row[4]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[4]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[4]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[4]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[4]['descripcion']) ?></p>  
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="news-card">
            <img src="<?= $row[5]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[5]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[5]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[5]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[5]['descripcion']) ?></p>  
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="news-card">
            <img src="<?= $row[6]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <span class="news-tag"><?= htmlspecialchars($row[6]['categoria']) ?></span>
              <a href="./views/news.php?id=<?= htmlspecialchars($row[6]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($row[6]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($row[6]['descripcion']) ?></p>  
            </div> 
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <!-- COLUMNA PRINCIPAL -->
      <div class="col-md-8">
        <button style="margin: 10px;">
          <img src="img/publicidad2.jpeg" alt="" class="banner">
        </button>
        <?php
          $contador = 0;
          $mostradas = 0;
          $result->data_seek(0);

          while ($row = $result->fetch_assoc()) {
            if ($contador < 11) { $contador++; continue; }
            if ($mostradas >= 5) break;
        ?>
          <div class="card mb-3">
            <div class="row row-no-gap">

              <div class="col-md-4">
                <img src="<?= $row['crop3'] ?>" class="card-img-left">
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
          $contador++;
          $mostradas++;
          }
        ?>
      </div>
      <!-- SIDEBAR -->
      <div class="col-md-4">
        <div class="sidebar-wrapper">
          <div class="card sidebar-card">
            <button>
              <img src="img/publicidad.jpeg" class="card-img-top">
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
</div>
<?php
  // Incluye el pie de página público y scripts
  include("./layout/footer.php");