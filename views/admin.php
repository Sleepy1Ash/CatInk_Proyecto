<?php 
// Página de administración (contenido principal de administración)
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
    // KPIs
    $kpis = $con->query("
        SELECT
            COUNT(*) AS total_noticias,
            SUM(CASE WHEN fecha_publicacion <= NOW() THEN 1 ELSE 0 END) AS publicadas,
            SUM(CASE WHEN fecha_publicacion > NOW() THEN 1 ELSE 0 END) AS programadas,
            SUM(vistas) AS total_vistas,
            SUM(likes) AS total_likes
        FROM noticias
    ")->fetch_assoc();

    $tiempoTotal = $con->query("
        SELECT COALESCE(SUM(tiempo_segundos),0) AS tiempo_total
        FROM noticias_stats
    ")->fetch_assoc()['tiempo_total'];
    // Últimas noticias (top 5)
    $resultNoticias = $con->query("
        SELECT 
            n.id,
            n.titulo,
            n.descripcion,
            n.crop2,
            n.vistas,
            n.likes,
            n.fecha_publicacion,
            COALESCE(SUM(ns.tiempo_segundos), 0) AS tiempo_total_stats
        FROM noticias n
        LEFT JOIN noticias_stats ns ON n.id = ns.noticia_id
        GROUP BY 
            n.id, n.titulo, n.descripcion, n.crop3, n.vistas, n.fecha_publicacion
        ORDER BY n.fecha_publicacion DESC
        LIMIT 5
    ");

    $ultimasNoticias = [];
    while($row = $resultNoticias->fetch_assoc()){
        $ultimasNoticias[] = $row;
    }
?>
<div class="container-fluid">
    <!-- Saludo y título -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Bienvenido, <?= htmlspecialchars($fila['usuario']) ?></h1>
        <a href="crear.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nueva Noticia</a>
    </div>
    <br>
    <!-- KPIs Cards -->
    <div class="row">
        <div class="col-md-65">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">KPIs</h5>
                    <div class="row g-3 mb-4">
                        <?php 
                        $cards = [
                            ['titulo'=>'Noticias', 'valor'=>$kpis['total_noticias'], 'icon'=>'bi-newspaper', 'color'=>'bg-primary'],
                            ['titulo'=>'Publicadas', 'valor'=>$kpis['publicadas'], 'icon'=>'bi-check-circle', 'color'=>'bg-success'],
                            ['titulo'=>'Programadas', 'valor'=>$kpis['programadas'], 'icon'=>'bi-clock', 'color'=>'bg-warning'],
                            ['titulo'=>'Vistas', 'valor'=>number_format($kpis['total_vistas']), 'icon'=>'bi-eye', 'color'=>'bg-info'],
                            ['titulo'=>'Likes', 'valor'=>number_format($kpis['total_likes']), 'icon'=>'bi-heart', 'color'=>'bg-danger'],
                            ['titulo'=>'Tiempo (min)', 'valor'=>number_format($tiempoTotal/60), 'icon'=>'bi-stopwatch', 'color'=>'bg-secondary'],
                        ];

                        foreach($cards as $card): ?>
                            <div class="col-md-2 col-6">
                                <div class="card shadow-sm text-center h-100">
                                    <div class="card-body">
                                        <div class="mb-2"><i class="bi <?= $card['icon'] ?> fs-3 <?= $card['color'] ?>"></i></div>
                                        <h4 class="mb-0"><?= $card['valor'] ?></h4>
                                        <small class="text-muted"><?= $card['titulo'] ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-55">
            <div class="card card-filter">
                <div class="card-body">
                    <h5 class="card-title">Filtros de Estadísticas</h5>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="filterFechaInicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="filterFechaInicio" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="filterFechaFin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="filterFechaFin" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="filterApply">Aplicar Filtros</label>
                            <button class="btn btn-secondary w-100" onclick="loadGlobalStats()">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     
    

    <!-- SECCIÓN DE GRÁFICAS GLOBALES -->
    

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
    <!-- Últimas noticias -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Últimas Noticias</h5>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach($ultimasNoticias as $noticia):
                    $desc = $noticia['descripcion'] ?? '';
                    $descCorta = mb_strimwidth($desc, 0, 80, '...');
                    $img = !empty($noticia['crop2']) ? "./../".$noticia['crop2'] : "./../img/placeholder.jpg";
                ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm news-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <small><?= date('d/m/Y H:i', strtotime($noticia['fecha_publicacion'])) ?></small>
                            </div>
                            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                            <div class="card-body">
                                <h5 class="card-title text-truncate" title="<?= htmlspecialchars($noticia['titulo']) ?>">
                                    <?= htmlspecialchars($noticia['titulo']) ?>
                                </h5>
                                <p class="card-text small text-muted"><?= htmlspecialchars($descCorta) ?></p>
                            </div>
                            <div class="card-footer d-flex justify-content-between small text-muted">
                                <span>Vistas <i class="bi bi-eye me-1"></i> <?= $noticia['vistas'] ?></span>
                                <span>Tiempo <i class="bi bi-clock me-1"></i> <?= number_format($noticia['tiempo_total_stats']/60, 0) ?>m</span>
                                <span>Likes <i class="bi bi-heart me-1"></i> <?= $noticia['likes'] ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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

        fetch(`./../controllers/obtener_estadisticas_globales.php?${params}`)
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
                    'tiempo',
                    'Tiempo de visualización (s)'
                );
            })
            .catch(console.error);
    }

    function loadLikesStats() {
        const fechaInicio = document.getElementById('filterFechaInicio').value;
        const fechaFin = document.getElementById('filterFechaFin').value;

        fetch(`./../controllers/obtener_estadisticas_likes.php?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`)
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
?>