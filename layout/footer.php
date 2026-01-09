
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- SCRIPT TEMA -->
<script>
  const themeToggleBtn = document.getElementById("themeToggle");
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute("data-bs-theme");
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    document.documentElement.setAttribute("data-bs-theme", newTheme);
    localStorage.setItem("theme", newTheme);
    themeToggleBtn.textContent =
      newTheme === "dark" ? "☀️" : "🌙";
  }
  themeToggleBtn.addEventListener("click", toggleTheme);
  // Cargar tema guardado
  window.addEventListener("DOMContentLoaded", () => {
    const savedTheme = localStorage.getItem("theme") || "light";
    document.documentElement.setAttribute("data-bs-theme", savedTheme);
    themeToggleBtn.textContent =
      savedTheme === "dark" ? "☀️" : "🌙";
  });
</script>
<script>
  const carousel = document.getElementById('carouselExampleCaptions');
  const indicators = document.querySelectorAll('.indicator-avatar circle');
  const duration = 5000; // MISMO que data-bs-interval
  function startProgress(index) {
    indicators.forEach(circle => {
      circle.style.transition = 'none';
      circle.style.strokeDashoffset = '100';
    });
    void indicators[index].offsetWidth; // force reflow
    indicators[index].style.transition = `stroke-dashoffset ${duration}ms linear`;
    indicators[index].style.strokeDashoffset = '0';
  }
  // Inicial
  window.addEventListener('DOMContentLoaded', () => {
    startProgress(0);
  });
  // Cambio automático
  carousel.addEventListener('slide.bs.carousel', e => {
    startProgress(e.to);
  });
  // Click manual
  document.querySelectorAll('.custom-indicators button')
    .forEach((btn, index) => {
      btn.addEventListener('click', () => {
        startProgress(index);
      });
    });
</script>
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
</body>
</html>
