<?php
// Convertidor de imagenes a base64 y guardado en BD
function guardarImagenBase64WebpConId($base64, $noticiaId, $crop, $calidad = 80) {
  if (empty($base64)) return null;

  if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $base64)) {
    return null;
  }

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

// Controlador para gestionar las noticias
include("../data/conexion.php");
// Obtencion de informacion del formulario
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$categoria = $_POST['categoria'];
$autor = $_POST['autor'];
$contenido = $_POST['contenido'];
$fecha_publicacion = $_POST['fecha_publicacion'] ?? date('Y-m-d H:i:s');
// Validacion basica
if (
  empty($titulo) ||
  empty($descripcion) ||
  empty($contenido)
) {
  die("Datos incompletos");
}
// Insercion en la base de datos con preparacion
$sql = "INSERT INTO noticias
(titulo, descripcion, categoria, autor, contenido, fecha_publicacion)
VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $con->prepare($sql);
$stmt->bind_param(
  "ssssss",
  $titulo,
  $descripcion,
  $categoria, 
  $autor,
  $contenido,
  $fecha_publicacion
);
$stmt->execute();

$noticiaId = $con->insert_id;
$crop1 = guardarImagenBase64WebpConId($_POST['crop1'] ?? null, $noticiaId, 'crop1');
$crop2 = guardarImagenBase64WebpConId($_POST['crop2'] ?? null, $noticiaId, 'crop2');
$crop3 = guardarImagenBase64WebpConId($_POST['crop3'] ?? null, $noticiaId, 'crop3');

// Actualizacion de rutas
$update = $con->prepare("
  UPDATE noticias
  SET crop1 = ?, crop2 = ?, crop3 = ?
  WHERE id = ?
");
$update->bind_param("sssi", $crop1, $crop2, $crop3, $noticiaId);
$update->execute();
// Redireccionamiento
header("Location: ./../views/admin.php");
exit;
?>