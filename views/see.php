<?php
    include("./../layout/headerAdmin.php");
    include("./../data/conexion.php");
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        header("Location: ./../views/contenidos.php");
        exit;
    }
    $stmt = $con->prepare("SELECT * FROM noticias, noticias_stats, noticia_likes 
    WHERE noticias.id = ? 
    AND noticias.id = noticias_stats.noticia_id 
    AND noticias.id = noticia_likes.noticia_id");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if (!$row) {
        header("Location: ./../views/contenidos.php");
        exit;
    }
    $noticiaId = $row['id'];
    function quillPreview($html, $limit = 500) {
    // 1. Quitar HTML
    $text = trim(strip_tags(html_entity_decode($html)));

    // 2. Cortar sin romper UTF-8
    if (mb_strlen($text) > $limit) {
        return mb_substr($text, 0, $limit) . '...';
    }

    return $text;
}

?>
<div class="container-fluid">
    <h1>Noticia: <?php echo $row['titulo']; ?></h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <img src="./../<?= htmlspecialchars($row['crop1'] ?? 'img/placeholder.jpg') ?>" class="img-card-left" alt="...">
                </div>
                <div class="card-body">
                    <p class="card-text text-muted mt-2"><?= htmlspecialchars($row['descripcion']) ?></p>
                    <p class="card-text text-muted mt-2"><small><?= htmlspecialchars(quillPreview($row['contenido'])) ?></small></p>  
                </div>
                <div class="card-footer">
                    <h6>Vistas Totales (Global):<i class="bi bi-eye"></i> <?= $row['vistas'] ?></h6>
                    <h6>Likes Totales (Global):<i class="bi bi-hand-thumbs-up"></i> <?= $row['likes'] ?></h6>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Estadísticas</h3>
                </div>
                <!-- Sección de Gráficas -->
                <div class="card-body">
                    <!-- Controles de Filtro -->
                    <div class="card-filter">
                        <div class="card-body">
                            <h5 class="card-title">Filtros de Estadísticas</h5>
                            <div class="row">
                                <div class="col">
                                    <label for="filterFechaInicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="filterFechaInicio" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                                </div>
                                <div class="col">
                                    <label for="filterFechaFin" class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="filterFechaFin" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col">
                                    <label for="filterApply">Aplicar Filtros</label>
                                    <button class="btn btn-secondary w-100" onclick="loadGlobalStats()">
                                        <i class="bi bi-funnel"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <span id="filterInfo"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Comparativa de Vistas</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px;">
                                        <canvas id="globalChartVistas"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Comparativa de Tiempo (Segundos)</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px;">
                                        <canvas id="globalChartTiempo"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">Comparativa de Likes</h5>
                                </div>
                                <div class="card-body">
                                <div style="height: 300px;">
                                    <canvas id="globalChartLikes"></canvas>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">Comparativa de Likes por region</h5>
                                </div>
                                <div class="card-body">
                                    <div style="height: 300px;">
                                        <canvas id="globalChartLikesRegion"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
</div>
<script>
    // Almacena todas las instancias de Chart.js
    const charts = {};

    document.addEventListener("DOMContentLoaded", () => {
        loadGlobalStats();
        loadLikesStats();
    });

    function loadGlobalStats() {
        const fechaInicio = document.getElementById('filterFechaInicio').value;
        const fechaFin = document.getElementById('filterFechaFin').value;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        });

        fetch(`./../controllers/obtener_estadisticas.php?noticia_id=${noticiaId}&${params}`)
            .then(res => res.json())
            .then(data => {
                renderAreaChart(
                    'globalChartVistas',
                    data,
                    'vistas',
                    'Vistas'
                );
                renderAreaChart(
                    'globalChartTiempo',
                    data,
                    'tiempoPromedio',
                    'Tiempo de visualización (s)'
                );
            })
            .catch(console.error);
    }

    function loadLikesStats() {
        const fechaInicio = document.getElementById('filterFechaInicio').value;
        const fechaFin = document.getElementById('filterFechaFin').value;
        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        });

        fetch(`./../controllers/obtener_likes.php?noticia_id=${noticiaId}&${params}`)
            .then(r => r.json())
            .then(data => {
                renderAreaChart(
                    'globalChartLikes',
                    data,
                    'likes',
                    'Likes'
                );
                renderBarChart(
                    'globalChartLikesRegion',
                    data.geo.paises,
                    'Likes por país'
                );
            })
            .catch(console.error);
    }

    /**
     * Renderiza un gráfico de área (líneas rellenas)
     */
    function renderAreaChart(canvasId, data, metric, labeltext) {
        const ctx = document.getElementById(canvasId).getContext('2d');

        // Destruir gráfico previo si existe
        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }

        const colors = [
            'rgba(75, 192, 192, 0.35)',
            'rgba(54, 162, 235, 0.35)',
            'rgba(255, 99, 132, 0.35)',
            'rgba(255, 159, 64, 0.35)',
            'rgba(153, 102, 255, 0.35)'
        ];

        const datasets = Object.entries(data.categorias).map(([cat, values], i) => ({
            label: cat,
            data: values[metric],
            fill: true,
            tension: 0.4,
            borderWidth: 2,
            backgroundColor: colors[i % colors.length],
            borderColor: colors[i % colors.length].replace('0.35', '1')
        }));

        charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    title: {
                        display: true,
                        text: labeltext
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            pointStyleWidth: 20,
                            pointStyleHeight: 20,
                            font: {
                                size: 16,
                                weight: 'italic'
                            }
                        },
                        onHover: e => e.native && (e.native.target.style.cursor = 'pointer'),
                        onLeave: e => e.native && (e.native.target.style.cursor = 'default'),
                        onClick: (e, legendItem, legend) => {
                            const index = legendItem.datasetIndex;
                            const chart = legend.chart;

                            // Mostrar / ocultar dataset
                            chart.setDatasetVisibility(index, !chart.isDatasetVisible(index));
                            chart.update();
                        }
                    },
                    tooltip: {
                        callbacks: {
                            footer: items =>
                                `Total: ${items.reduce((a, i) => a + i.parsed.y, 0)}`
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
    /**
     * Renderiza un gráfico de barras
     */
    function renderBarChart(canvasId, geoData, title) {
        const ctx = document.getElementById(canvasId).getContext('2d');

        if (charts[canvasId]) {
            charts[canvasId].destroy();
        }

        charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: geoData.labels,
                datasets: [{
                    label: title,
                    data: geoData.values,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                    display: true,
                    text: title
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
<?php
// Se incluye el footerAdmin que cierra el main y añade scripts
include("./../layout/footerAdmin.php");
?>>