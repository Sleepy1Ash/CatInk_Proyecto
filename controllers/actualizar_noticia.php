<?php
include("../data/conexion.php");

function guardarImagenBase64WebpConId($base64, $noticiaId, $crop, $calidad = 80) {
  if (empty($base64)) return null;
  if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $base64)) return null;
  $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
  $binario = base64_decode($base64);
  if ($binario === false) return null;
  $imagen = imagecreatefromstring($binario);
  if (!$imagen) return null;
  $timestamp = time();
  $nombre = "noticia_{$noticiaId}_{$crop}_{$timestamp}.webp";
  $rutaFisica = __DIR__ . "/../img/noticias/" . $nombre;
  imagewebp($imagen, $rutaFisica, $calidad);
  imagedestroy($imagen);
  return "img/noticias/" . $nombre;
}

$id = intval($_POST['id'] ?? 0);
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$categoria = $_POST['categoria'] ?? [];
$categoriaCsv = implode(',', $categoria);
$contenido = $_POST['contenido'] ?? '';
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d H:i:s');

if ($id <= 0 || empty($titulo) || empty($descripcion) || empty($contenido)) {
  header("Location: ./../views/contenidos.php");
  exit;
}

$update = $con->prepare("
  UPDATE noticias
  SET titulo = ?, descripcion = ?, categoria = ?, contenido = ?, fecha_publicacion = ?
  WHERE id = ?
");
$update->bind_param("sssssi", $titulo, $descripcion, $categoriaCsv, $contenido, $fecha_publicacion, $id);
$update->execute();

$stmt = $con->prepare("SELECT crop1, crop2, crop3 FROM noticias WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$actual = $res->fetch_assoc();
$c1 = $actual['crop1'] ?? null;
$c2 = $actual['crop2'] ?? null;
$c3 = $actual['crop3'] ?? null;

$new1 = guardarImagenBase64WebpConId($_POST['crop1'] ?? null, $id, 'crop1');
$new2 = guardarImagenBase64WebpConId($_POST['crop2'] ?? null, $id, 'crop2');
$new3 = guardarImagenBase64WebpConId($_POST['crop3'] ?? null, $id, 'crop3');

if ($new1 || $new2 || $new3) {
  $c1 = $new1 ?: $c1;
  $c2 = $new2 ?: $c2;
  $c3 = $new3 ?: $c3;
  $updImgs = $con->prepare("UPDATE noticias SET crop1 = ?, crop2 = ?, crop3 = ? WHERE id = ?");
  $updImgs->bind_param("sssi", $c1, $c2, $c3, $id);
  $updImgs->execute();
}

header("Location: ./../views/contenidos.php?msg=actualizado");
exit;
?>
