<?php
include("./../layout/header.php");
include("./../data/conexion.php");

$id = intval($_GET['id'] ?? 2);

$sql = "SELECT * FROM noticias WHERE id = ? AND fecha_publicacion <= NOW()";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$noticia = $result->fetch_assoc();
$sql2 = "SELECT nombre FROM usuarios WHERE id_u = ?";
$stmt2 = $con->prepare($sql2);
$stmt2->bind_param("i", $noticia['autor']);
$stmt2->execute();
$result2 = $stmt2->get_result();
$autor_nombre = $result2->fetch_assoc()['nombre'];
if (!$noticia) {
  die("Noticia no encontrada");
}
?>
<!-- Contenido de noticias aquí -->
<div class="container-noticia">
    <span><?= htmlspecialchars($noticia['categoria']) ?></span>
    <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>

    <p class="descripcion"><?= nl2br(htmlspecialchars($noticia['descripcion'])) ?></p>

    <p class="meta">
    Por <strong><?= htmlspecialchars($autor_nombre) ?></strong> —
    <?= date("d/m/Y H:i", strtotime($noticia['fecha_publicacion'])) ?>
    </p>
    <img src="./../<?= htmlspecialchars($noticia['crop1']) ?>" alt="" class="img-titular">
    <div class="ql-editor">
        <?= $noticia['contenido'] ?>
    </div>
</div>
<script>
  fetch("/controllers/sumarvistas.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "noticia_id=<?= $id ?>"
  })
  .then(response => response.json())
  .then(data => console.log("Vistas actualizadas:", data))
  .catch(error => console.error("Error al sumar vista:", error));
</script>
<script>
  let inicio = Date.now();
  let enviado = false;

  function enviarTiempo() {
    if (enviado) return;
    enviado = true;

    let tiempo = Math.floor((Date.now() - inicio) / 1000);

    navigator.sendBeacon(
      "/controllers/guardartiempo.php",
      new URLSearchParams({
        noticia_id: "<?= $id ?>",
        tiempo: tiempo
      })
    );
  }

  // cuando el usuario sale o cambia de pestaña
  window.addEventListener("beforeunload", enviarTiempo);
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
      enviarTiempo();
    }
  });
</script>
<?php
include("./../layout/footer.php");
?>