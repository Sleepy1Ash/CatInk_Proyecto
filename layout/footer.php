<!-- Fin del contenido principal -->
</main>
<!-- Script local: reemplaza comportamientos de Bootstrap (colapso, tema, carrusel) -->
<script src="/CatInk_Proyecto/CSS/scripts.js"></script>
<script>
let searchTimeout = null;
const input = document.getElementById('searchInput');
if (input) {
  input.addEventListener('keyup', function () {
    clearTimeout(searchTimeout);
    const q = this.value.trim();
    searchTimeout = setTimeout(() => {
      if (q.length >= 2) {
        // Redirige al controlador de búsqueda/categoría
        window.location.href = `/CatInk_Proyecto/views/categoria.php?q=${encodeURIComponent(q)}`;
      }
    }, 400);
  });
}
</script>
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
        © 2026 CatInk. Todos los derechos reservados.
      </small>
    </div>
  </div>
</footer>
</body>
</html>
