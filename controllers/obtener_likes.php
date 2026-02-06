<?php
include("../data/conexion.php");
header('Content-Type: application/json');
try {
    // Validar noticia
    if (!isset($_GET['noticia_id'])) {
        echo json_encode(['error' => 'Falta noticia_id']);
        exit;
    }
    $noticiaId = intval($_GET['noticia_id']);
    // Fechas opcionales
    $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
    $fechaFin    = $_GET['fecha_fin'] ?? date('Y-m-d');
    $fechaInicioSql = $fechaInicio . ' 00:00:00';
    $fechaFinSql    = $fechaFin . ' 23:59:59';
    // Calcular rango en días
    $dias = (strtotime($fechaFin) - strtotime($fechaInicio)) / 86400;
    // Determinar modo y agrupación
    if ($dias <= 15) {
        $modo = 'diario';
        $groupBy = "DATE(nl.fecha)";
        $interval = 'P1D';
    } elseif ($dias <= 60) {
        $modo = 'semanal';
        $groupBy = "YEARWEEK(nl.fecha, 1)";
        $interval = 'P7D';
    } else {
        $modo = 'quincenal';
        $groupBy = "CONCAT(YEAR(nl.fecha), '-', CEIL(DAY(nl.fecha)/15))";
        $interval = 'P15D';
    }
    // ==============================
    // 1️⃣ Likes por periodo
    // ==============================
    $sql = "
        SELECT
            {$groupBy} AS periodo,
            COUNT(*) AS likes,
            DATE(nl.fecha) AS label_fecha
        FROM noticia_likes nl
        WHERE nl.noticia_id = ?
          AND nl.fecha BETWEEN ? AND ?
        GROUP BY periodo
        ORDER BY label_fecha ASC
    ";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iss", $noticiaId, $fechaInicioSql, $fechaFinSql);
    $stmt->execute();
    $result = $stmt->get_result();
    $rawData = [];
    while ($row = $result->fetch_assoc()) {
        $rawData[$row['label_fecha']] = (int)$row['likes'];
    }
    // ==============================
    // 2️⃣ Normalizar fechas (rellenar ceros)
    // ==============================
    $labels = [];
    $likes = [];
    $start = new DateTime($fechaInicio);
    $end   = new DateTime($fechaFin);
    $end->modify('+1 day');
    $period = new DatePeriod($start, new DateInterval($interval), $end);
    foreach ($period as $date) {
        $label = $date->format('Y-m-d');
        $labels[] = $label;
        $likes[] = $rawData[$label] ?? 0;
    }
    // ==============================
    // 3️⃣ Likes por región (país / estado)
    // ==============================
    $sqlGeo = "
        SELECT pais, estado, COUNT(*) AS total
        FROM noticia_likes
        WHERE noticia_id = ?
          AND fecha BETWEEN ? AND ?
        GROUP BY pais, estado
        ORDER BY total DESC
    ";
    $stmtGeo = $con->prepare($sqlGeo);
    $stmtGeo->bind_param("iss", $noticiaId, $fechaInicioSql, $fechaFinSql);
    $stmtGeo->execute();
    $resultGeo = $stmtGeo->get_result();
    $paises = [];
    $estados = [];
    while ($row = $resultGeo->fetch_assoc()) {
        $pais = $row['pais'] ?: 'Desconocido';
        $estado = $row['estado'] ?: 'Desconocido';
        $paises[$pais] = ($paises[$pais] ?? 0) + (int)$row['total'];
        $estados[$estado] = ($estados[$estado] ?? 0) + (int)$row['total'];
    }
    $geo = [
        'paises' => [
            'labels' => array_keys($paises),
            'values' => array_values($paises)
        ],
        'estados' => [
            'labels' => array_keys($estados),
            'values' => array_values($estados)
        ]
    ];
    // ==============================
    // 4️⃣ Respuesta final
    // ==============================
    echo json_encode([
        'modo' => $modo,
        'labels' => $labels,
        'likes' => $likes,
        'geo' => $geo
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>