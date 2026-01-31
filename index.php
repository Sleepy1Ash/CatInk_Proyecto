<?php
    include("./layout/header.php");
    include("./data/conexion.php");
    // todas las noticias
    $stmt = $con -> prepare("SELECT titulo, descripcion, categoria, crop1, crop2,
    crop3, fecha_publicacion FROM noticias WHERE fecha_publicacion<=NOW() ORDER BY id DESC");
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
<!-- CONTENIDO -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
  <div class="carousel-indicators custom-indicators">
    <?php
      $contador = 0;
      $result->data_seek(0); // volver al inicio del result

      while ($row = $result->fetch_assoc()) {
          if ($contador >= 5) break;
    ?>
        <button type="button"
            data-bs-target="#carouselExampleCaptions"
            data-bs-slide-to="<?= $contador ?>"
            class="<?= $contador === 0 ? 'active' : '' ?>">
            
            <div class="indicator-avatar">
                <img src="./<?= $row['crop1'] ?>" alt="<?= htmlspecialchars($row['titulo']) ?>">
                <svg viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="16"></circle>
                </svg>
            </div>
        </button>
    <?php
          $contador++;
      }
    ?>
  </div>
  <div class="carousel-inner">
    <?php
      $contador = 0;
      $result->data_seek(0); // volver a empezar

      while ($row = $result->fetch_assoc()) {
          if ($contador >= 5) break;
    ?>
          <div class="carousel-item <?= $contador === 0 ? 'active' : '' ?>">
              <img src="<?= $row['crop2'] ?>" class="carousel-img" alt="<?= htmlspecialchars($row['titulo']) ?>">

              <div class="carousel-caption caption-md">
                  <span class="carousel-tag"><?= htmlspecialchars($row['categoria']) ?></span>

                  <h5>
                      <a href="./views/news.php" class="carousel-link">
                          <?= htmlspecialchars($row['titulo']) ?>
                      </a>
                  </h5>

                  <p><?= htmlspecialchars($row['descripcion']) ?></p>
              </div>
          </div>
    <?php
          $contador++;
      }
    ?>
  </div>
</div>
<div class="container mt-5">
  <div class="container-fluid">
    <div id="cards-start"></div>
    <div class="row">
      <!-- Columna principal: ocupa 8/12 en pantallas md+ y 100% en móvil -->
      <?php
        $contador = 0;
        $mostradas = 0;

        $result->data_seek(0); // aseguramos inicio

        while ($row = $result->fetch_assoc()) {

            // Saltar las primeras 5 (carrusel)
            if ($contador < 5) {
                $contador++;
                continue;
            }

            // Mostrar solo 5 cards
            if ($mostradas >= 5) {
                break;
            }
      ?>
          <div class="col-md-8 main-content">
              <div class="card mb-3">
                  <div class="row row-no-gap">
                      
                      <div class="col-md-4">
                          <img src="<?= $row['crop3'] ?>"
                              class="card-img-left"
                              alt="<?= htmlspecialchars($row['titulo']) ?>">
                      </div>

                      <div class="col-md-8">
                          <div class="card-body">

                              <span class="badge bg-secondary mb-2">
                                  <?= htmlspecialchars($row['categoria']) ?>
                              </span>

                              <h5 class="card-title">
                                  <a href="./views/news.php"
                                    class="text-decoration-none">
                                      <?= htmlspecialchars($row['titulo']) ?>
                                  </a>
                              </h5>

                              <p class="card-text">
                                  <?= htmlspecialchars($row['descripcion']) ?>
                              </p>

                              <p class="card-text">
                                  <small class="text-muted">
                                      <?= date('d M Y', strtotime($row['fecha_publicacion'])) ?>
                                  </small>
                              </p>

                          </div>
                      </div>

                  </div>
              </div>
          </div>
      <?php
            $mostradas++;
            $contador++;
        }
      ?>
      <!-- Columna secundaria: ocupa 4/12 en pantallas md+ -->
      <div class="col-md-4">
        <div class="sidebar-wrapper">
          <div class="card sidebar-card">

            <!-- Imagen destacada -->
            <a href="./views/news.php">
              <img src="img/destacada.jpg" class="card-img-top" alt="Destacado">
            </a>

            <div class="card-body">

              <!-- Lo más nuevo -->
              <h5 class="card-title mb-2">🆕 Lo más nuevo</h5>
              <ul class="list-group list-group-flush mb-3">
                <?php while ($row = $ultimas->fetch_assoc()) { ?>
                  <li class="list-group-item px-0">
                    <a href="./views/news.php?id=<?= $row['id'] ?>"
                      class="text-decoration-none">
                      <?= htmlspecialchars($row['titulo']) ?>
                    </a>
                  </li>
                <?php } ?>
              </ul>

              <!-- Lo más popular -->
              <h5 class="card-title mb-2">🔥 Lo más popular</h5>
              <ul class="list-group list-group-flush">
                <?php while ($row = $populares->fetch_assoc()) { ?>
                  <li class="list-group-item px-0">
                    <a href="./views/news.php?id=<?= $row['id'] ?>"
                      class="text-decoration-none">
                      <?= htmlspecialchars($row['titulo']) ?>
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
<div id="site-footer"></div>
<script>
  const sidebar = document.querySelector('.sidebar-wrapper');
  const cardsStart = document.querySelector('#cards-start');
  const footer = document.querySelector('#site-footer');

  let inCardsZone = false;
  let footerVisible = false;

  const updateSidebar = () => {
    if (inCardsZone && !footerVisible) {
      sidebar.classList.add('is-visible');
    } else {
      sidebar.classList.remove('is-visible');
    }
  };

  // Observer: inicio de cards
  const cardsObserver = new IntersectionObserver(
    ([entry]) => {
      inCardsZone = !entry.isIntersecting;
      updateSidebar();
    },
    { rootMargin: '-100px 0px 0px 0px' }
  );

  // Observer: footer
  const footerObserver = new IntersectionObserver(
    ([entry]) => {
      footerVisible = entry.isIntersecting;
      updateSidebar();
    },
    { rootMargin: '0px' }
  );

  cardsObserver.observe(cardsStart);
  footerObserver.observe(footer);
</script>


<?php
  // Incluye el pie de página público y scripts
  include("./layout/footer.php");