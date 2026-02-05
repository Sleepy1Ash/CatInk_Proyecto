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

    function parseCategorias($s) {
        $s = trim($s ?? '');
        if ($s === '') return [];
        if (preg_match('/^\s*\[/', $s)) {
            $decoded = json_decode($s, true);
            if (is_array($decoded)) {
                return array_filter(array_map('trim', $decoded));
            }
        }
        $s = preg_replace('/[\\[\\]\\"\\\']/', '', $s);
        $parts = preg_split('/[;,|\/]+/', $s, -1, PREG_SPLIT_NO_EMPTY);
        return array_filter(array_map('trim', $parts));
    }
?>
<!-- CONTENIDO -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
  <?php
    $slider = array_slice($noticias, 0, 5);
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
          <?php
            $cats = parseCategorias($row['categoria'] ?? '');
            foreach ($cats as $cat):
          ?>
            <span class="carousel-tag"><?= htmlspecialchars($cat) ?></span>
          <?php endforeach; ?>

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
    <center>
      <h2>Últimas Noticias</h2>
      <br>
    </center>
    <?php
      $ultimasNoticias = array_slice($noticias, 0, 7);
    ?>
    <div class="row">
      <div class="row">
        <div class="col-md-8">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[0]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[0]['categoria'] ?? '');
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[0]['id']) ?>" class="news-link"> 
                <h3><?= htmlspecialchars($ultimasNoticias[0]['titulo']) ?></h3>
              </a>
              <p><?= htmlspecialchars($ultimasNoticias[0]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[1]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[1]['categoria'] ?? '');
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[1]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($ultimasNoticias[1]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[1]['descripcion']) ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[2]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[2]['categoria'] ?? ''); 
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[2]['id']) ?>" class="news-link">   
                <h3><?= htmlspecialchars($ultimasNoticias[2]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[2]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[3]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[3]['categoria'] ?? ''); 
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[3]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($ultimasNoticias[3]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[3]['descripcion']) ?></p>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[4]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[4]['categoria'] ?? '');
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[4]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($ultimasNoticias[4]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[4]['descripcion']) ?></p>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[5]['crop3'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[5]['categoria'] ?? '');
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[5]['id']) ?>" class="news-link"> 
                <h3><?= htmlspecialchars($ultimasNoticias[5]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[5]['descripcion']) ?></p>  
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="news-card">
            <img src="<?= $ultimasNoticias[6]['crop2'] ?>" alt="">
            <div class="news-overlay">
              <?php
                $cats = parseCategorias($ultimasNoticias[6]['categoria'] ?? '');
                foreach ($cats as $cat):
              ?>
                <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
              <?php endforeach; ?>
              <a href="./views/news.php?id=<?= htmlspecialchars($ultimasNoticias[6]['id']) ?>" class="news-link">
                <h3><?= htmlspecialchars($ultimasNoticias[6]['titulo']) ?></h3> 
              </a> 
              <p><?= htmlspecialchars($ultimasNoticias[6]['descripcion']) ?></p>  
            </div> 
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="row">
      <!-- COLUMNA PRINCIPAL -->
      <div class="col-md-8">
        <button style="margin: 10px; border-radius: 10px;">
          <a href="./views/news.php?id=<?= htmlspecialchars($row[6]['id']) ?>" class="news-link">
            <img src="img/publicidad2.jpeg" alt="" class="banner">
          </a>
        </button>
        <center>
          <h2>Noticias más recientes</h2>
        </center>
        <?php
          $contador = 0;
          $mostradas = 0;
          $result->data_seek(0);

          while ($row = $result->fetch_assoc()) {
            if ($contador < 7) { $contador++; continue; }
            if ($mostradas >= 5) break;
        ?>
          <div class="card mb-3">
            <div class="row row-no-gap">

              <div class="col-md-4">
                <img src="<?= $row['crop3'] ?>" class="card-img-left">
              </div>

              <div class="col-md-8">
                <div class="card-body">
                  <?php
                    $cats = parseCategorias($row['categoria'] ?? '');
                    foreach ($cats as $cat):
                  ?>
                    <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                  <?php endforeach; ?>

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
        <center>
          <h2>Noticias más populares</h2>
        </center>
        <div class="row scrollable-cards-row">
          <?php
            $contador = 0;
            $mostradas = 0;
            $result->data_seek(0);

            while ($row = $result->fetch_assoc()) {
              if ($contador < 12) { $contador++; continue; }
              if ($mostradas >= 3) break;
          ?>
            <div class="col">
              <div class="card">
                <img src="<?= $row['crop3'] ?>" class="card-img-top" alt="">
                <div class="card-body">
                  <?php
                    $cats = parseCategorias($row['categoria'] ?? '');
                    foreach ($cats as $cat):
                  ?>
                    <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                  <?php endforeach; ?>
                  <h5 class="card-title">
                    <a href="./views/news.php?id=<?= htmlspecialchars($row['id']) ?>" class="news-link"><?= htmlspecialchars($row['titulo']) ?></a>
                  </h5>
                  <p><?= htmlspecialchars($row['descripcion']) ?></p> 
                  <small class="text-muted"><?= date('d M Y', strtotime($row['fecha_publicacion'])) ?></small>
                </div>
              </div>
            </div>
          <?php
            $contador++;
            $mostradas++;
            }
          ?>
        </div>
        <center>
          <h2>Noticias anteriores</h2>
        </center>
        <br>
        <?php
          $contador = 0;
          $mostradas = 0;
          $result->data_seek(0);

          while ($row = $result->fetch_assoc()) {
            if ($contador < 15) { $contador++; continue; }
            if ($mostradas >= 5) break;
        ?>
          <div class="card mb-3">
            <div class="row row-no-gap">

              <div class="col-md-4">
                <img src="<?= $row['crop3'] ?>" class="card-img-left">
              </div>

              <div class="col-md-8">
                <div class="card-body">
                  <?php
                    $cats = parseCategorias($row['categoria'] ?? '');
                    foreach ($cats as $cat):
                  ?>
                    <span class="news-tag"><?= htmlspecialchars($cat) ?></span>
                  <?php endforeach; ?>

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
