<?php
    date_default_timezone_set("America/Mexico_City");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require("./../../PHPMailer/src/PHPMailer.php");
    require("./../../PHPMailer/src/Exception.php");
    require("./../../PHPMailer/src/SMTP.php");
    include("./../../data/conexion.php");
    $hora = "SELECT hora FROM programacion_correos LIMIT 1";
    $stmtHora = $con->prepare($hora);
    $stmtHora->execute();
    $resultHora = $stmtHora->get_result();
    $rowHora = $resultHora->fetch_assoc();
    $horaProgramada = $rowHora['hora'];
    if (abs(strtotime(date("H:i:s")) - strtotime($horaProgramada)) <= 60) {
    $hoy = date("Y-m-d H:i:s");
    $sql = "SELECT * FROM noticias WHERE DATE(fecha_publicacion) <= ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $hoy);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $noticias = [];
    while ($row = $resultado->fetch_assoc()) {
        $noticias[] = $row;
    }
    if (empty($noticias)) {
        die("No se encontraron noticias para el día $hoy");
    } else {
        echo "Noticias encontradas: " . count($noticias);
    }
    $mail = new PHPMailer(true);
    $contenidoNoticias = '';
    $mail->addEmbeddedImage(
        __DIR__ . '/logo_alt.png',
        'banner',
        'logo_alt.png'
    );
    foreach ($noticias as $index => $noticia) {
        $descripcion = strip_tags($noticia['descripcion']); // quitar HTML
        $descripcion = mb_strimwidth($descripcion, 0, 100, '...');
        $webp = 'https://catink.com.mx/' . $noticia['crop3'];
        $png = __DIR__ . "/logo_temp_{$index}.png"; // archivos temporales únicos
        // Convertir WebP a PNG
        $image = imagecreatefromwebp($webp);
        imagepng($image, $png);
        imagedestroy($image);

        // Adjuntar imagen convertida al objeto PHPMailer
        $mail->addEmbeddedImage($png, "logo{$index}", "logo.png"); // cid único
        // Concatenar HTML, referenciando la cid única
        $contenidoNoticias .= "
                                <table width='100%' cellpadding='0' cellspacing='0' border='0' 
                                    style='background:#ffffff;margin-bottom:15px;border-radius:10px;overflow:hidden;'>
                                <tr class='stack-column'>
                                <!-- IMAGEN -->
                                <td width='240' valign='top' class='card-padding'>
                                <img 
                                src='cid:logo{$index}' 
                                width='220'
                                class='stack-img'
                                style='
                                width:100%;
                                max-width:220px;
                                height:auto;
                                display:block;
                                border-radius:10px;
                                border:0;
                                '>
                                </td>
                                <!-- TEXTO -->
                                <td valign='top' class='card-padding' style='font-family:Arial,sans-serif;color:#000;'>
                                <a href='https://catink.com.mx/views/news.php?id={$noticia['id']}'
                                style='
                                background:#EF3363;
                                color:#EF3363;
                                text-decoration:none;
                                '>
                                <h3 style='margin:0;'>{$noticia['titulo']}</h3>
                                </a>
                                <p style='margin:10px; '>
                                {$descripcion}
                                </p>
                                </td>
                                </tr>
                                </table>
                                ";

    }
    $plantillaPath = __DIR__ . "/diarias.html";
    if (!file_exists($plantillaPath)) {
        die("Error: plantilla no encontrada en $plantillaPath");
    }
    $plantilla = file_get_contents($plantillaPath);
    $plantilla = str_replace("{{noticias}}", $contenidoNoticias, $plantilla);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';   // Cambia por tu SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'faustoperezortega15@gmail.com'; // Tu email
        $mail->Password = 'aiunnaqifeqwjrrx';               // Tu contraseña
        $mail->SMTPSecure = 'tls';                    // o 'ssl'
        $mail->Port = 587;                            // o 465 si SSL

        $mail->setFrom('faustoperezortega15@gmail.com', 'CatInk News');

        // Lista de destinatarios (puede ser dinámica)
        $sqlUsuarios = "SELECT correo, nombre_completo FROM suscripciones";
        $resUsuarios = $con->query($sqlUsuarios);

        $mail->isHTML(true);
        $mail->Subject = 'Resumen diario de noticias';
        $mail->Body = $plantilla;
        while($user = $resUsuarios->fetch_assoc()){
            $mail->clearAddresses();
            $mail->addAddress($user['correo'], $user['nombre_completo']);
            $mail->send();
        }
        // Eliminar archivos temporales
        foreach ($noticias as $index => $noticia) {
            $png = __DIR__ . "/logo_temp_{$index}.png";
            if (file_exists($png)) {
                unlink($png);
            }
        }
        echo "Correo enviado correctamente a todos los usuarios suscritos.";
        header("Location: ./../../index.php?msg=enviado");

    } catch (Exception $e) {
        echo "Error al enviar: {$mail->ErrorInfo}";
    }
    }