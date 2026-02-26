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
<script>
  document.addEventListener("DOMContentLoaded", function(){
      if(window.instgrm){
          window.instgrm.Embeds.process();
      }
  });
</script>
<!-- Pie de página: columnas, enlaces y barra inferior -->
<footer class="site-footer mt-5">
  <div class="container py-5">
    <div class="row">
      <!-- Logo / descripción -->
      <div class="col">
        <img id="logo" src="" alt="CatInk Logo">
        <p class="footer-text">
          Noticias, anime, videojuegos y cultura digital.
        </p>
      </div>
      <!-- Páginas hermanas -->
      <div class="col">
        <h4 class="footer-title">Enlaces de interes</h4>
        <ul class="footer-links">
          <li><a href="/CatInk_Proyecto/views/nosotros.php"><i class="bi bi-building-fill"></i> Nosotros</a></li>
          <li><a href="/CatInk_Proyecto/views/terminos.php"><i class="bi bi-file-earmark-text-fill"></i> Terminos y Condiciones</a></li>
          <li><a href="/CatInk_Proyecto/views/unete.php"><i class="bi bi-briefcase-fill"></i> Unete a nuestro equipo</a></li>
          <li><a href="/CatInk_Proyecto/views/suscripcion.php" aria-label="Suscribete"><i class="bi bi-bookmark-star-fill"></i> Suscribete</a></li>
          <li><a href="/CatInk_Proyecto/views/contactanos.php"><i class="bi bi-envelope-fill"></i> Contactanos</a></li>
        </ul>
      </div>
      <!-- Redes sociales -->
      <div class="col">
        <h4 class="footer-title">Síguenos</h4>
        <div class="social-links">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Twitter / X"><i class="bi bi-twitter-x"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
          <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
          <!--<a href="#" aria-label="Twitch"><i class="bi bi-twitch"></i></a>-->
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
