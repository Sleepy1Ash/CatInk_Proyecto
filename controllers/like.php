<?php
include("../data/conexion.php");
header('Content-Type: application/json');
// Obtener la IP del usuario
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    return $_SERVER['REMOTE_ADDR'];
}

$noticia_id = intval($_POST['noticia_id'] ?? 0);
$ip = getUserIP();
// API simple de geolocalización
$geo = json_decode(file_get_contents("http://ip-api.com/json/$ip"), true);

$pais = $geo['country'] ?? 'Desconocido';
$estado = $geo['regionName'] ?? 'Desconocido';

if ($noticia_id <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID inválido']);
    exit;
}

try {
    $con->begin_transaction();

    // Intentar registrar el like
    $stmt = $con->prepare(
        "INSERT INTO noticia_likes (noticia_id, ip, pais, estado) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $noticia_id, $ip, $pais, $estado);    
    $stmt->execute();

    // Sumar like
    $con->query(
        "UPDATE noticias SET likes = likes + 1 WHERE id = $noticia_id"
    );

    $con->commit();
    echo json_encode(['ok' => true]);

} catch (mysqli_sql_exception $e) {
    $con->rollback();

    /* Error 1062 = clave duplicada (ya dio like)
    if ($e->getCode() == 1062) {
        echo json_encode(['ok' => false, 'msg' => 'Ya diste like']);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Error interno']);
    }*/
}
