<?php
include("../data/conexion.php");
header('Content-Type: application/json');

if (!isset($_GET['noticia_id'])) {
    echo json_encode(['error' => 'Falta el parámetro noticia_id']);
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
    $groupBy = "DATE(fecha)";
    $labelFormat = "DATE(fecha)";
    $formatLabel = fn($row) => date('d/m', strtotime($row['label_fecha']));
} elseif ($dias <= 60) {
    $modo = 'semanal';
    $groupBy = "YEARWEEK(fecha, 1)";
    $labelFormat = "MIN(DATE(fecha))";
    $formatLabel = fn($row) => 'Semana ' . date('W', strtotime($row['label_fecha']));
} else {
    $modo = 'quincenal';
    $groupBy = "CONCAT(YEAR(fecha), '-', CEIL(DAY(fecha)/15))";
    $labelFormat = "MIN(DATE(fecha))";
    $formatLabel = fn($row) => date('d/m/Y', strtotime($row['label_fecha']));
}

// Consulta dinámica
$sql = "
    SELECT 
        {$groupBy} AS periodo,
        {$labelFormat} AS label_fecha,
        COUNT(*) AS lecturas,
        AVG(tiempo_segundos) AS tiempo_promedio,
        SUM(tiempo_segundos) AS tiempo_total
    FROM noticias_stats
    WHERE noticia_id = ?
      AND fecha BETWEEN ? AND ?
    GROUP BY periodo
    ORDER BY label_fecha ASC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("iss", $noticiaId, $fechaInicioSql, $fechaFinSql);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$vistas = [];
$tiempoPromedio = [];
$tiempoTotal = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $formatLabel($row);
    $vistas[] = intval($row['lecturas']);
    $tiempoPromedio[] = round(floatval($row['tiempo_promedio']), 1);
    $tiempoTotal[] = intval($row['tiempo_total']);
}

echo json_encode([
    'modo' => $modo,
    'labels' => $labels,
    'vistas' => $vistas,
    'tiempoPromedio' => $tiempoPromedio,
    'tiempoTotal' => $tiempoTotal
]);
?>
