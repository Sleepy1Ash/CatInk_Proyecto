<?php
include("../data/conexion.php");

header('Content-Type: application/json');

if (!isset($_GET['noticia_id']) || !isset($_GET['rango'])) {
    echo json_encode(['error' => 'Faltan parámetros']);
    exit;
}

$noticiaId = intval($_GET['noticia_id']);
$rango = $_GET['rango']; // 'diario', 'semanal', 'mensual'

$labels = [];
$vistas = [];
$tiempoPromedio = [];
$tiempoTotal = [];

// Configuración de la consulta según el rango
switch ($rango) {
    case 'semanal':
        // Últimas 12 semanas
        $sql = "SELECT 
                    YEARWEEK(fecha, 1) as periodo, 
                    MIN(DATE(fecha)) as fecha_inicio,
                    COUNT(*) as lecturas, 
                    AVG(tiempo_segundos) as tiempo_promedio,
                    SUM(tiempo_segundos) as tiempo_total
                FROM noticias_stats 
                WHERE noticia_id = ? 
                  AND fecha >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
                GROUP BY YEARWEEK(fecha, 1)
                ORDER BY periodo ASC";
        $formatLabel = function($row) {
            return 'Semana ' . date('W', strtotime($row['fecha_inicio']));
        };
        break;

    case 'mensual':
        // Últimos 12 meses
        $sql = "SELECT 
                    DATE_FORMAT(fecha, '%Y-%m') as periodo, 
                    COUNT(*) as lecturas, 
                    AVG(tiempo_segundos) as tiempo_promedio,
                    SUM(tiempo_segundos) as tiempo_total
                FROM noticias_stats 
                WHERE noticia_id = ? 
                  AND fecha >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY periodo
                ORDER BY periodo ASC";
        $formatLabel = function($row) {
            setlocale(LC_TIME, 'es_ES.UTF-8');
            $dateObj = DateTime::createFromFormat('!Y-m', $row['periodo']);
            return $dateObj->format('M Y');
        };
        break;

    case 'diario':
    default:
        // Últimos 15 días (para que no sea demasiado ancho)
        $sql = "SELECT 
                    DATE(fecha) as periodo, 
                    COUNT(*) as lecturas, 
                    AVG(tiempo_segundos) as tiempo_promedio,
                    SUM(tiempo_segundos) as tiempo_total
                FROM noticias_stats 
                WHERE noticia_id = ? 
                  AND fecha >= DATE_SUB(NOW(), INTERVAL 15 DAY)
                GROUP BY periodo
                ORDER BY periodo ASC";
        $formatLabel = function($row) {
            return date('d/m', strtotime($row['periodo']));
        };
        break;
}

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $noticiaId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $labels[] = $formatLabel($row);
    $vistas[] = intval($row['lecturas']);
    $tiempoPromedio[] = round(floatval($row['tiempo_promedio']), 1);
    $tiempoTotal[] = intval($row['tiempo_total']);
}

echo json_encode([
    'labels' => $labels,
    'vistas' => $vistas,
    'tiempoPromedio' => $tiempoPromedio,
    'tiempoTotal' => $tiempoTotal
]);
?>