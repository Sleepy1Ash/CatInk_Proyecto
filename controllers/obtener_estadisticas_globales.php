<?php
include("../data/conexion.php");

header('Content-Type: application/json');

try {

    // Parámetros
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');
    $categoria   = $_GET['categoria'] ?? '';

    $fechaInicioSql = $fechaInicio . ' 00:00:00';
    $fechaFinSql    = $fechaFin . ' 23:59:59';

    // SQL compatible con ONLY_FULL_GROUP_BY
    $sql = "
        SELECT 
            n.titulo,
            COUNT(ns.id_s) AS total_vistas,
            COALESCE(SUM(ns.tiempo_segundos), 0) AS total_tiempo
        FROM noticias n
        LEFT JOIN noticias_stats ns 
            ON n.id = ns.noticia_id
            AND ns.fecha BETWEEN ? AND ?
        WHERE (? = '' OR n.categoria = ?)
        GROUP BY n.id, n.titulo
        ORDER BY total_vistas DESC
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param(
        "ssss",
        $fechaInicioSql,
        $fechaFinSql,
        $categoria,
        $categoria
    );

    $stmt->execute();

    // Variables de resultado (NO arrays)
    $tituloDB  = '';
    $vistasDB  = 0;
    $tiempoDB  = 0;

    $stmt->bind_result($tituloDB, $vistasDB, $tiempoDB);

    // Arrays de salida
    $labels = [];
    $vistas = [];
    $tiempo = [];

    while ($stmt->fetch()) {

        // Corte seguro de texto (sin mbstring obligatorio)
        if (function_exists('mb_strimwidth')) {
            $shortTitle = mb_strimwidth($tituloDB, 0, 20, "...");
        } else {
            $shortTitle = substr($tituloDB, 0, 20) . "...";
        }

        $labels[] = $shortTitle;
        $vistas[] = (int)$vistasDB;
        $tiempo[] = (int)$tiempoDB;
    }

    echo json_encode([
        'labels' => $labels,
        'vistas' => $vistas,
        'tiempo' => $tiempo
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}
