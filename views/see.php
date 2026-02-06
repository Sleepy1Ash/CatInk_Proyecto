<?php
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: ./../views/contenidos.php");
    exit;
}
// Obtener noticia con estadísticas globales
$stmt = $con->prepare("
    SELECT n.*,
           COALESCE(SUM(ns.tiempo_segundos),0) AS tiempo_total,
           COUNT(DISTINCT ns.id_s) AS vistas,
           COUNT(DISTINCT nl.id_l) AS likes
    FROM noticias n
    LEFT JOIN noticias_stats ns ON n.id = ns.noticia_id
    LEFT JOIN noticia_likes nl ON n.id = nl.noticia_id
    WHERE n.id = ?
    GROUP BY n.id
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if (!$row) {
    header("Location: ./../views/contenidos.php");
    exit;
}
$noticiaId = $row['id'];
// Función para mostrar preview de contenido Quill
function quillPreview($html, $limit = 500) {
    $text = trim(strip_tags(html_entity_decode($html)));
    if (mb_strlen($text) > $limit) return mb_substr($text, 0, $limit) . '...';
    return $text;
}
?>
<div class="container-fluid">
    <h1>Noticia: <?= htmlspecialchars($row['titulo']) ?></h1>
    <div class="row mt-3">
        <!-- Tarjeta de Noticia -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <img src="./../<?= htmlspecialchars($row['crop1'] ?? 'img/placeholder.jpg') ?>" class="card-img-top" alt="Imagen noticia">
                <div class="card-body">
                    <p class="text-muted"><?= htmlspecialchars($row['descripcion']) ?></p>
                    <p><small><?= htmlspecialchars(quillPreview($row['contenido'])) ?></small></p>
                </div>
                <div class="card-footer">
                    <p class="mb-1">Vistas Totales: <i class="bi bi-eye"></i> <?= number_format($row['vistas']) ?></p>
                    <p class="mb-0">Likes Totales: <i class="bi bi-hand-thumbs-up"></i> <?= number_format($row['likes']) ?></p>
                </div>
            </div>
        </div>
        <!-- Estadísticas -->
        <div class="col-md-8 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Estadísticas</h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col">
                            <label for="filterFechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" id="filterFechaInicio" class="form-control" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                        </div>
                        <div class="col">
                            <label for="filterFechaFin" class="form-label">Fecha Fin</label>
                            <input type="date" id="filterFechaFin" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col d-flex align-items-end">
                            <button class="btn btn-secondary w-100" onclick="loadGlobalStats(); loadLikesStats();">
                                <i class="bi bi-funnel"></i> Aplicar
                            </button>
                        </div>
                    </div>
                    <!-- Gráficos -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">Comparativa de Vistas</div>
                                <div class="card-body">
                                    <canvas id="globalChartVistas" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">Comparativa de Tiempo (s)</div>
                                <div class="card-body">
                                    <canvas id="globalChartTiempo" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-danger text-white">Comparativa de Likes</div>
                                <div class="card-body">
                                    <canvas id="globalChartLikes" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">Likes por Región</div>
                                <div class="card-body">
                                    <canvas id="globalChartLikesRegion" style="height: 300px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="filterInfo" class="mt-2 text-muted"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
const charts = {};
document.addEventListener("DOMContentLoaded", () => {
    loadGlobalStats();
    loadLikesStats();
});
function loadGlobalStats() {
    const fechaInicio = document.getElementById('filterFechaInicio').value;
    const fechaFin = document.getElementById('filterFechaFin').value;
    fetch(`./../controllers/obtener_estadisticas.php?noticia_id=<?= $noticiaId ?>&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
        .then(res => res.json())
        .then(data => {
            renderAreaChart('globalChartVistas', data, 'vistas', 'Vistas');
            renderAreaChart('globalChartTiempo', data, 'tiempoPromedio', 'Tiempo promedio (s)');
        })
        .catch(console.error);
}
function loadLikesStats() {
    const fechaInicio = document.getElementById('filterFechaInicio').value;
    const fechaFin = document.getElementById('filterFechaFin').value;
    fetch(`./../controllers/obtener_likes.php?noticia_id=<?= $noticiaId ?>&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
        .then(res => res.json())
        .then(data => {
            renderAreaChart('globalChartLikes', data, 'likes', 'Likes');
            if(data.geo && data.geo.paises) renderBarChart('globalChartLikesRegion', data.geo.paises, 'Likes por país');
        })
        .catch(console.error);
}
function renderAreaChart(canvasId, data, metric, labelText) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    if(charts[canvasId]) charts[canvasId].destroy();
    charts[canvasId] = new Chart(ctx, {
        type: 'line',
        data: { labels: data.labels, datasets:[{ label: labelText, data: data[metric], fill:true, tension:0.4, borderWidth:2, backgroundColor:'rgba(54,162,235,0.2)', borderColor:'rgba(54,162,235,1)' }] },
        options: { responsive:true, plugins:{ title:{ display:true, text:labelText } }, scales:{ y:{ beginAtZero:true } } }
    });
}
function renderBarChart(canvasId, geoData, labelText) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    if(charts[canvasId]) charts[canvasId].destroy();
    charts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: { labels: geoData.labels, datasets:[{ label: labelText, data: geoData.values, backgroundColor:'rgba(255,206,86,0.3)', borderColor:'rgba(255,206,86,1)', borderWidth:1 }] },
        options: { responsive:true, plugins:{ title:{ display:true, text:labelText } }, scales:{ y:{ beginAtZero:true } } }
    });
}
</script>
<?php include("./../layout/footerAdmin.php"); ?>