<?php
include("./../data/conexion.php");

// ========================
// DATOS
// ========================
$id      = $_POST['id'];
$nombre  = $_POST['nombre'];
$usuario = $_POST['usuario'];
$email   = $_POST['email'];
$password = $_POST['password'] ?? "";

// ========================
// FUNCIÓN PERMISOS BITMASK
// ========================
function calcPerm($arr){
    $perm = 0;
    if(isset($arr)){
        foreach($arr as $v){
            $perm += (int)$v;
        }
    }
    return $perm;
}
$perm_publicidad    = calcPerm($_POST['publicidad'] ?? []);
$perm_noticias      = calcPerm($_POST['noticias'] ?? []);
$perm_categorias    = calcPerm($_POST['categorias'] ?? []);
$perm_suscripciones = calcPerm($_POST['suscripciones'] ?? []);
$perm_usuarios      = calcPerm($_POST['usuarios'] ?? []);
// ========================
// SI NO CAMBIA CONTRASEÑA
// ========================
if(empty($password)){
    $stmt = $con->prepare("
        UPDATE usuarios SET
        nombre=?, usuario=?, correo=?,
        perm_publicidad=?, perm_noticias=?, perm_categorias=?, perm_suscripciones=?, perm_usuarios=?
        WHERE id_u=?
    ");
    $stmt->bind_param(
        "sssiiiiii",
        $nombre, $usuario, $email,
        $perm_publicidad, $perm_noticias, $perm_categorias, $perm_suscripciones, $perm_usuarios,
        $id
    );
// ========================
// SI CAMBIA CONTRASEÑA
// ========================
} else {
    if($password !== $_POST['confirm_password']){
        header("Location: ./../views/editaru.php?id=$id&error=pass");
        exit;
    }
    $passHash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $con->prepare("
        UPDATE usuarios SET
        nombre=?, usuario=?, correo=?, pass=?,
        perm_publicidad=?, perm_noticias=?, perm_categorias=?, perm_suscripciones=?, perm_usuarios=?
        WHERE id_u=?
    ");
    $stmt->bind_param(
        "sssssiiiii i",
        $nombre, $usuario, $email, $passHash,
        $perm_publicidad, $perm_noticias, $perm_categorias, $perm_suscripciones, $perm_usuarios,
        $id
    );
}
// ========================
// EJECUTAR
// ========================
if($stmt->execute()){
    header("Location: ./../views/usuarios.php?update=ok");
} else {
    die("Error al actualizar: " . $stmt->error);
}