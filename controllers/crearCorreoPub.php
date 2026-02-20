<?php
session_start();
include("./aclcontroller.php");
proteger('correos','crear');
include("../data/conexion.php");
include("../views/helpers/img.php");
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $titulo     = $_POST['titulo'];
    $contenido  = $_POST['contenido'];
    $url        = $_POST['url'];
    $envio = date("Y-m-d H:i:s", strtotime($_POST['envio']));
    $rutaFisica = __DIR__ . "/../img/correo/" . $nombre;
    // Crear carpeta si no existe
    if (!is_dir(__DIR__ . "/../img/correo")) {
        mkdir(__DIR__ . "/../img/correo", 0777, true);
    }
    $imagenNombre = convertirImagenAWebp(
        $_FILES['imagen'],
        './../uploads',
        1200,
        80
    );
    if(!$imagenNombre){
        die("Error al procesar imagen");
    }
    $stmt = $con->prepare("
        INSERT INTO publicidad 
        (titulo, contenido, imagen, url_c, envio) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssss",
        $titulo,
        $contenido,
        $imagenNombre,
        $url,
        $envio
    );
    if($stmt->execute()){
        echo "<script>alert('Correo guardado correctamente');</script>";
    }else{
        echo "<script>alert('Error al guardar');</script>";
    }
}