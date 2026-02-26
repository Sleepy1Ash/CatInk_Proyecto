<?php
date_default_timezone_set("America/Mexico_City");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require("./../../PHPMailer/src/PHPMailer.php");
require("./../../PHPMailer/src/Exception.php");
require("./../../PHPMailer/src/SMTP.php");
include("./../../data/conexion.php");

$nombre = $_POST['nombre'];
$email = $_POST['email'];
$mensaje = $_POST['message'];
// Crear objeto PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'faustoperezortega15@gmail.com';
    $mail->Password   = '';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('faustoperezortega15@gmail.com', 'CatInk News');

    // Destinatarios (puedes poner dinámicos desde base de datos)
    $mail->addAddress('al222211174@gmail.com', 'Fausto Pérez Ortega');

    $mail->isHTML(true);
    $mail->Subject = "Solicitud de ascesoramiento por parte de CatInk";

    // Construir el contenido HTML
    $html = "
    <div style='font-family: Arial, sans-serif; max-width:600px; margin:auto; background:#f9f9f9; padding:20px; border-radius:10px;'>
        <h2 style='color:#EF3363;'>Solicitud de ascesoramiento por parte de CatInk</h2>
        <p style='color:#333;'><strong>Nombre:</strong> $nombre</p>
        <p style='color:#333;'><strong>Correo:</strong> $email</p>
        <p style='color:#333;'><strong>Mensaje:</strong> $mensaje</p>
    </div>
    ";

    $mail->Body = $html;

    $mail->send();

    echo "Correo enviado correctamente.";
    header("Location: ./../contactanos.php?success=1");
    exit();

} catch (Exception $e) {
    // Eliminar temporal incluso si falla
    if(file_exists($tmpPng)) unlink($tmpPng);
    echo "Error al enviar: {$mail->ErrorInfo}";
}