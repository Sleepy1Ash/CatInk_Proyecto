<?php
include("../data/conexion.php");
header('Content-Type: application/json');

try {
    // Parámetros
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');

    $fechaInicioSql = $fechaInicio . ' 00:00:00';
    $fechaFinSql    = $fechaFin . ' 23:59:59';

    // Calcular rango en días
    $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / 86400;

    if ($dias <= 15) {
        $modo = 'diario';
        $groupBy = "DATE(ns.fecha)";
        $labelFormat = "DATE(ns.fecha)";
    } elseif ($dias <= 60) {
        $modo = 'semanal';
        $groupBy = "YEARWEEK(ns.fecha, 1)";
        $labelFormat = "MIN(DATE(ns.fecha))";
    } else {
        $modo = 'quincenal';
        $groupBy = "CONCAT(YEAR(ns.fecha), '-', CEIL(DAY(ns.fecha)/15))";
        $labelFormat = "MIN(DATE(ns.fecha))";
    }

    $sql = "
        SELECT
            n.categoria,
            {$groupBy} AS periodo,
            {$labelFormat} AS label_fecha,
            COUNT(ns.id_s) AS vistas,
            SUM(ns.tiempo_segundos) AS tiempo
        FROM noticias_stats ns
        JOIN noticias n ON n.id = ns.noticia_id
        WHERE ns.fecha BETWEEN ? AND ?
        GROUP BY n.categoria, periodo
        ORDER BY label_fecha ASC
    ";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $fechaInicioSql, $fechaFinSql);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels = [];
    $dataCategorias = [];

    while ($row = $result->fetch_assoc()) {
        $label = $row['label_fecha'];

        if (!in_array($label, $labels)) {
            $labels[] = $label;
        }

        $cat = $row['categoria'];
        
        if (!isset($dataCategorias[$cat])) {
            $dataCategorias[$cat] = [
                'vistas' => [],
                'tiempo' => []
            ];
        }

        $dataCategorias[$cat]['vistas'][$label] = (int)$row['vistas'];
        $dataCategorias[$cat]['tiempo'][$label] = (int)$row['tiempo'];

    }

    // Normalizar arrays (rellenar ceros)
    foreach ($dataCategorias as $cat => $metrics) {
        $finalVistas = [];
        $finalTiempo = [];

        foreach ($labels as $l) {
            $finalVistas[] = $metrics['vistas'][$l] ?? 0;
            $finalTiempo[] = $metrics['tiempo'][$l] ?? 0;
        }

        $dataCategorias[$cat] = [
            'vistas' => $finalVistas,
            'tiempo' => $finalTiempo
        ];
    }


    echo json_encode([
        'modo' => $modo,
        'labels' => $labels,
        'categorias' => $dataCategorias
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
