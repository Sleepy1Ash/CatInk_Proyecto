<?php
function convertirImagenAWebp($archivo, $carpetaDestino, $maxAncho = 1200, $calidad = 80){
    if($archivo['error'] !== 0){
        return false;
    }
    $tmp = $archivo['tmp_name'];
    $mime = mime_content_type($tmp);
    switch($mime){
        case 'image/jpeg':
            $origen = imagecreatefromjpeg($tmp);
        break;
        case 'image/png':
            $origen = imagecreatefrompng($tmp);
            imagepalettetotruecolor($origen);
            imagealphablending($origen, true);
            imagesavealpha($origen, true);
        break;
        case 'image/gif':
            $origen = imagecreatefromgif($tmp);
        break;
        default:
            return false;
    }
    $anchoOriginal = imagesx($origen);
    $altoOriginal  = imagesy($origen);
    if($anchoOriginal > $maxAncho){
        $nuevoAncho = $maxAncho;
        $nuevoAlto  = ($altoOriginal * $maxAncho) / $anchoOriginal;
    }else{
        $nuevoAncho = $anchoOriginal;
        $nuevoAlto  = $altoOriginal;
    }
    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
    imagealphablending($destino, false);
    imagesavealpha($destino, true);
    imagecopyresampled(
        $destino, $origen,
        0, 0, 0, 0,
        $nuevoAncho, $nuevoAlto,
        $anchoOriginal, $altoOriginal
    );
    $nombre = uniqid() . ".webp";
    $rutaFinal = $carpetaDestino . "/" . $nombre;
    imagewebp($destino, $rutaFinal, $calidad);
    imagedestroy($origen);
    imagedestroy($destino);
    return $nombre;
}