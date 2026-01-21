
<!-- Fin del contenido principal -->
</main>
<!-- Script local: reemplaza comportamientos de Bootstrap (colapso, tema, carrusel) -->
<script src="/CatInk_Proyecto/CSS/scripts.js"></script>
<!-- Pie de página: columnas, enlaces y barra inferior -->
<footer class="site-footer mt-5">
  <div class="container py-5">
    <div class="row gy-4">
      <!-- Logo / descripción -->
      <div class="col-md-4">
        <h5 class="footer-title">CatInk</h5>
        <p class="footer-text">
          Noticias, anime, videojuegos y cultura digital.
        </p>
      </div>
      <!-- Páginas hermanas -->
      <div class="col-md-4">
        <h6 class="footer-title">Páginas hermanas</h6>
        <ul class="footer-links">
          <li><a href="#">AnimeWorld</a></li>
          <li><a href="#">GameZone</a></li>
          <li><a href="#">GeekNews</a></li>
        </ul>
      </div>
      <!-- Redes sociales -->
      <div class="col-md-4">
        <h6 class="footer-title">Síguenos</h6>
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

    </div>
  </div>
  <!-- Derechos -->
  <div class="footer-bottom">
    <div class="container text-center">
      <small>
        © 2026 TuSitio. Todos los derechos reservados.
      </small>
    </div>
  </div>
</footer>
<!-- Modal de confirmación -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal">
        <div class="modal-header" id="modalTitle">
            <!-- Título dinámico -->
        </div>
        <div class="modal-body">
            <span class="text-danger small">Esta acción no se puede deshacer.</span>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary btn-cancel">Cancelar</button>
            <form action="../controllers/eliminar_noticia.php" method="POST" id="modalForm">
                <input type="hidden" name="id" id="modalId">
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
