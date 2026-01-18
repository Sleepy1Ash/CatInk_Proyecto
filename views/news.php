<?php
include("./../layout/header.php");
include("./../data/conexion.php");

$id = intval($_GET['id'] ?? 1);

$sql = "SELECT * FROM noticias WHERE id = ? AND fecha_publicacion >= NOW()";
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
<?php
include("./../layout/footer.php");
?>