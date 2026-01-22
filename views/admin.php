<?php 
// Página de administración (contenido principal de administración)
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
?>
<h1>Panel de Administración - Estadísticas</h1>
<div class="container-fluid">
<?php
    // Obtener todas las noticias con sus estadísticas agregadas
    // Usamos LEFT JOIN para obtener el tiempo total desde noticias_stats
    // Asumimos que n.vistas es el contador global de vistas
    $sqlNoticias = $con->prepare("
        SELECT 
            n.id,
            n.titulo,
            n.descripcion,
            n.crop3,
            n.vistas,
            n.fecha_publicacion,
            COALESCE(SUM(ns.tiempo_segundos), 0) AS tiempo_total_stats
        FROM noticias n
        LEFT JOIN noticias_stats ns ON n.id = ns.noticia_id
        GROUP BY 
            n.id, n.titulo, n.descripcion, n.crop3, n.vistas, n.fecha_publicacion
        ORDER BY n.fecha_publicacion DESC
    ");
    $sqlNoticias->execute();
    $resultNoticias = $sqlNoticias->get_result();

    $noticiasData = [];

    while ($noticia = $resultNoticias->fetch_assoc()) {
        $noticiasData[] = $noticia;
    }

?>

    <!-- SECCIÓN DE GRÁFICAS GLOBALES -->
    <div class="card mb-4 shadow-sm">
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

    <div class="row mb-5">
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

    <!-- SECCIÓN DE NOTICIAS (CARDS) -->
    <center>
        <br>
        <hr>
        <h3 class="titulos mb-4">Listado de Noticias</h3>
        <hr>
        <br>
    </center>
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        <?php foreach ($noticiasData as $noticia):
            $desc = $noticia['descripcion'] ?? '';
            $descCorta = function_exists('mb_strimwidth')
            ? mb_strimwidth($desc, 0, 80, "...")
            : substr($desc, 0, 80) . '...';
            ?>
            <div class="col">
                <div class="card news-card">
                    <!-- Imagen (crop3) -->
                    <img src="./../<?= htmlspecialchars($noticia['crop3'] ?? 'img/placeholder.jpg') ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                    
                    <div class="card-body">
                        <h5 class="card-title text-truncate" title="<?= htmlspecialchars($noticia['titulo']) ?>">
                            <?= htmlspecialchars($noticia['titulo']) ?>
                        </h5>
                        <p class="card-text small text-muted">
                            <?= htmlspecialchars($descCorta) ?>
                        </p>
                    </div>
                    
                    <div class="card-footer">
                        <span title="Vistas Totales">
                            <p>Vistas</p>
                            <i class="bi bi-eye me-1"></i> <?= $noticia['vistas'] ?>
                        </span>
                        <span title="Tiempo Total de Visualización">
                            <p>Tiempo</p>
                            <i class="bi bi-clock me-1"></i> <?= number_format($noticia['tiempo_total_stats'], 0) ?>s
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
let chartVistas = null;
let chartTiempo = null;

document.addEventListener("DOMContentLoaded", () => {
    loadGlobalStats();
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

function renderAreaChart(canvasId, data, metric, labelText) {
    const ctx = document.getElementById(canvasId).getContext('2d');

    let chartRef = canvasId === 'globalChartVistas' ? chartVistas : chartTiempo;
    if (chartRef) chartRef.destroy();

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

    const chart = new Chart(ctx, {
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
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        pointStyleWidth: 20,        // Ancho del icono (por defecto ~10)
                        pointStyleHeight: 20,       // Alto del icono (opcional, por defecto igual que width)
                        font: {
                            size: 16,       // Tamaño de la letra en píxeles (por defecto es 12)
                            weight: 'italic', // Opcional: 'normal', 'bold', etc.   
                        },
                    },
                    onHover: (event, legendItem, legend) => {
                        // Cambia el cursor a "pointer" solo para la leyenda
                        event.native ? event.native.target.style.cursor = 'pointer' : null;
                    },
                    onLeave: (event, legendItem, legend) => {
                        event.native ? event.native.target.style.cursor = 'default' : null;
                    },
                    onClick: (e, legendItem, legend) => {
                        // Usar el método interno toggleDataVisibility
                        const index = legendItem.datasetIndex;
                        const chart = legend.chart;
                        // Este es el correcto:
                        chart.setDatasetVisibility(index, !chart.isDatasetVisible(index));
                        chart.update();
                    }
                },
                tooltip: {
                    callbacks: {
                        footer: (items) => {
                            let total = 0;
                            items.forEach(i => total += i.parsed.y);
                            return `Total: ${total}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    if (canvasId === 'globalChartVistas') chartVistas = chart;
    else chartTiempo = chart;
}
</script>



<?php
// Se incluye el footerAdmin que cierra el main y añade scripts
include("./../layout/footerAdmin.php");
?>