<?php
// Controlador para gestionar las noticias
include("../data/conexion.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $titulo = $_POST['titulo'];
    $autor_id = $_POST['autor_id'];
    $imagen_destacada = $_POST['imagen_destacada_recortada'];
    $videos = $_POST['videos'];
    $imagenes = $_POST['imagenes'];// Array de imágenes subidas
    
    $parrafos = [];
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_POST["parrafo_$i"]) && !empty(trim($_POST["parrafo_$i"]))) {
            $parrafos[] = trim($_POST["parrafo_$i"]);
        }
    }
    $contenido = implode("\n\n", $parrafos);
    // Insertar noticia en la base de datos
    $stmt = $con->prepare("INSERT INTO noticias (titulo, autor_id, imagen_destacada, videos, contenido) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $titulo, $autor_id, $imagen_destacada, $videos, $contenido);
    if ($stmt->execute()) {
        header("Location: ./../views/admin.php?mensaje=noticia_creada");
        exit();
    } else {
        echo "Error al crear la noticia: " . $stmt->error;
    }
    $stmt->close();
}
?>