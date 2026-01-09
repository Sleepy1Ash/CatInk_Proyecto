<?php
    include("./layout/header.php");
?>
<!-- CONTENIDO -->
<div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
  <div class="carousel-indicators custom-indicators">
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active">
      <div class="indicator-avatar">
        <img src="img/maxresdefault.jpg" alt="">
        <svg viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="16"></circle>
        </svg>
      </div>
    </button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1">
      <div class="indicator-avatar">
        <img src="img/drx-skin-ashe-caitlyn-akali-maokai-kindred-aatrox-hd-wallpaper-uhdpaper.com-636@1@k.jpg" alt="">
        <svg viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="16"></circle>
        </svg>
      </div>
    </button>
    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2">
      <div class="indicator-avatar">
        <img src="img/heartsteel-ezreal-yone-kayn-aphelios-sett-ksante-skin-lol-splash-art-hd-wallpaper-uhdpaper.com-402@1@m.jpg" alt="">
        <svg viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="16"></circle>
        </svg>
      </div>
    </button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="img/maxresdefault.jpg" class="carousel-img" alt="...">
      <div class="carousel-caption caption-md">
        <span class="carousel-tag">Noticias</span>
        <h5>First slide label</h5>
        <p>Some representative placeholder content for the first slide.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/drx-skin-ashe-caitlyn-akali-maokai-kindred-aatrox-hd-wallpaper-uhdpaper.com-636@1@k.jpg" class="carousel-img" alt="...">
      <div class="carousel-caption caption-md">
        <span class="carousel-tag">Noticias</span>
        <h5>Second slide label</h5>
        <p>Some representative placeholder content for the second slide.</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="img/heartsteel-ezreal-yone-kayn-aphelios-sett-ksante-skin-lol-splash-art-hd-wallpaper-uhdpaper.com-402@1@m.jpg" class="carousel-img" alt="...">
      <div class="carousel-caption caption-md">
        <span class="carousel-tag">Noticias</span>
        <h5>Third slide label</h5>
        <p>Some representative placeholder content for the third slide.</p>
      </div>
    </div>
  </div>
</div>
<div class="container mt-5">
  <div class="container-fluid">
    <div class="row">
      <!-- Columna principal: ocupa 8/12 en pantallas md+ y 100% en móvil -->
      <div class="col-md-8">
        <div class="card mb-3">
          <div class="row row-no-gap">
            <div class="col-md-4">
              <img src="img/c.jpg" class="card-img-left" alt="...">
            </div>
            <div class="col-md-8">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Columna secundaria: ocupa 4/12 en pantallas md+ -->
      <div class="col-md-4">
        <div class="card">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">An item</li>
            <li class="list-group-item">A second item</li>
            <li class="list-group-item">A third item</li>
          </ul>
          <div class="card-footer">
            Card footer
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
  // Incluye el pie de página público y scripts
  include("./layout/footer.php");