<?php
include("../data/conexion.php");
header('Content-Type: application/json');
try {
    // ============================
    // Parámetros de fecha
    // ============================
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');
    $fechaInicioSql = $fechaInicio . ' 00:00:00';
    $fechaFinSql    = $fechaFin . ' 23:59:59';
    // ============================
    // Rango de fechas y agrupación
    // ============================
    $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / 86400;
    if ($dias <= 15) {
        $modo = 'diario';
        $groupBy = "DATE(nl.fecha)";
        $labelFormat = "DATE(nl.fecha)";
    } elseif ($dias <= 60) {
        $modo = 'semanal';
        $groupBy = "YEARWEEK(nl.fecha, 1)";
        $labelFormat = "MIN(DATE(nl.fecha))";
    } else {
        $modo = 'quincenal';
        $groupBy = "CONCAT(YEAR(nl.fecha), '-', CEIL(DAY(nl.fecha)/15))";
        $labelFormat = "MIN(DATE(nl.fecha))";
    }
    // ============================
    // Likes por categoría (JOIN normalizado)
    // ============================
    $sql = "
        SELECT
            c.nombre AS categoria,
            {$groupBy} AS periodo,
            {$labelFormat} AS label_fecha,
            COUNT(nl.id_l) AS likes
        FROM noticia_likes nl
        JOIN noticias n ON n.id = nl.noticia_id
        JOIN noticia_categoria nc ON nc.noticia_id = n.id
        JOIN categorias c ON c.id_c = nc.categoria_id
        WHERE nl.fecha BETWEEN ? AND ?
        GROUP BY c.nombre, periodo
        ORDER BY label_fecha ASC
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ss", $fechaInicioSql, $fechaFinSql);
    $stmt->execute();
    $result = $stmt->get_result();
    // ============================
    // Likes por región (país/estado)
    // ============================
    $sqlGeo = "
        SELECT
            pais,
            estado,
            COUNT(*) AS total
        FROM noticia_likes
        WHERE fecha BETWEEN ? AND ?
        GROUP BY pais, estado
        ORDER BY total DESC
    ";
    $stmtGeo = $con->prepare($sqlGeo);
    $stmtGeo->bind_param("ss", $fechaInicioSql, $fechaFinSql);
    $stmtGeo->execute();
    $resultGeo = $stmtGeo->get_result();
    $labels = [];
    $dataCategorias = [];
    $paises = [];
    $estados = [];
    // Procesar likes por región
    while ($row = $resultGeo->fetch_assoc()) {
        $pais = $row['pais'] ?: 'Desconocido';
        $estado = $row['estado'] ?: 'Desconocido';
        $paises[$pais] = ($paises[$pais] ?? 0) + (int)$row['total'];
        $estados[$estado] = ($estados[$estado] ?? 0) + (int)$row['total'];
    }
    // Procesar likes por categoría
    while ($row = $result->fetch_assoc()) {
        $label = $row['label_fecha'];
        if (!in_array($label, $labels)) $labels[] = $label;
        $cat = $row['categoria'];
        if (!isset($dataCategorias[$cat])) $dataCategorias[$cat] = ['likes' => []];
        $dataCategorias[$cat]['likes'][$label] = (int)$row['likes'];
    }
    // Normalizar arrays (rellenar ceros)
    foreach ($dataCategorias as $cat => $metrics) {
        $finalLikes = [];
        foreach ($labels as $l) {
            $finalLikes[] = $metrics['likes'][$l] ?? 0;
        }
        $dataCategorias[$cat] = ['likes' => $finalLikes];
    }
    // Preparar datos geográficos
    $geoData = [
        'paises' => [
            'labels' => array_keys($paises),
            'values' => array_values($paises)
        ],
        'estados' => [
            'labels' => array_keys($estados),
            'values' => array_values($estados)
        ]
    ];
    echo json_encode([
        'modo' => $modo,
        'labels' => $labels,
        'categorias' => $dataCategorias,
        'geo' => $geoData
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>