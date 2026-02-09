<?php
include("./../data/conexion.php");
header('Content-Type: application/json');
// Validar id
$id_c = intval($_POST['id_c'] ?? 0);
if($id_c <= 0) exit(json_encode(['error'=>'ID de categoría inválido']));
// Eliminar relaciones con noticias (opcional, si quieres eliminar automáticamente)
$stmt = $con->prepare("DELETE FROM noticia_categoria WHERE categoria_id=?");
$stmt->bind_param("i",$id_c);
$stmt->execute();
// Eliminar categoría
$stmt = $con->prepare("DELETE FROM categorias WHERE id_c=?");
$stmt->bind_param("i",$id_c);
if($stmt->execute()){
    echo json_encode(['success'=>true]);
}else{
    echo json_encode(['error'=>'No se pudo eliminar la categoría']);
}
