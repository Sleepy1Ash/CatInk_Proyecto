<?php
date_default_timezone_set("America/Mexico_City");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require(__DIR__."/../../PHPMailer/src/PHPMailer.php");
require(__DIR__."/../../PHPMailer/src/Exception.php");
require(__DIR__."/../../PHPMailer/src/SMTP.php");
include(__DIR__."/../../data/conexion.php");
$hoy = date("Y-m-d H:i:s");
// Consulta para obtener informacion de correos a enviar
$sql="SELECT * FROM correos_publicitarios WHERE envio >= ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('s', $hoy);
$stmt->execute();
$resultado = $stmt->get_result();
$correo = $resultado->fetch_assoc();
if(!$correo){
    die("No hay infromacion");
}
// Datos de ejemplo que se enviarían dinámicamente
$titulo   = $correo['titulo']; // Título de la noticia
$contenido = $correo['contenido']; // Contenido de la noticia
$webpPath  = $correo['imagen']; // URL WebP
$urlBoton  = $correo['url_c'];

// Nombre de archivo temporal único para PNG
$tmpPng = __DIR__ . "/temp_" . uniqid() . ".png";

// Convertir WebP a PNG
$image = imagecreatefromwebp("https://www.catink.com.mx/img/correo/".$webpPath);
if(!$image) {
    die("Error al cargar la imagen WebP");
}
imagepng($image, $tmpPng);
imagedestroy($image);

// Crear objeto PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'catink.oficial@gmail.com';
    $mail->Password   = 'lamcszfwuoftmlpv';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('catink.oficial@gmail.com', 'CatInk News');

    // Destinatarios (puedes poner dinámicos desde base de datos)
    $mail->addAddress('al222211174@gmail.com', 'Fausto Pérez Ortega');

    $mail->isHTML(true);
    $mail->Subject = $titulo;

    // Adjuntar la imagen convertida
    $mail->addEmbeddedImage($tmpPng, 'imagenNoticia', 'imagen.png');

    // Construir el contenido HTML
    $html = "
    <div style='font-family: Arial, sans-serif; max-width:600px; margin:auto; background:#f9f9f9; padding:20px; border-radius:10px;'>
        <h2 style='color:#EF3363;'>$titulo</h2>
        <p style='color:#333;'>$contenido</p>
        <img src='cid:imagenNoticia' style='width:100%; max-width:500px; border-radius:10px; margin:15px 0;' />
        <a href='$urlBoton' 
           style='display:inline-block; padding:10px 20px; background:#EF3363; color:#fff; text-decoration:none; border-radius:5px; margin-top:10px;'>
           Ver promocion
        </a>
    </div>
    ";

    $mail->Body = $html;

    $mail->send();

    // Eliminar la imagen temporal
    if(file_exists($tmpPng)) {
        unlink($tmpPng);
    }

    echo "Correo enviado correctamente.";

} catch (Exception $e) {
    // Eliminar temporal incluso si falla
    if(file_exists($tmpPng)) unlink($tmpPng);
    echo "Error al enviar: {$mail->ErrorInfo}";
}