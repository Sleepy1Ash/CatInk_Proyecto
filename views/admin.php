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
    $desc = $noticia['descripcion'] ?? '';
    $descCorta = function_exists('mb_strimwidth')
    ? mb_strimwidth($desc, 0, 80, "...")
    : substr($desc, 0, 80) . '...';

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
                    <label for="filterCategoria" class="form-label">Categoría</label>
                    <select class="form-select" id="filterCategoria">
                        <option value="">Todas</option>
                        <option value="Pelicualas">Películas</option>
                        <option value="Series">Series</option>
                        <option value="Cultura Pop">Cultura Pop</option>
                        <option value="Anime">Anime</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" onclick="loadGlobalStats()">
                        <i class="bi bi-funnel"></i> Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
        <?php foreach ($noticiasData as $noticia): ?>
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
                    
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span title="Vistas Totales">
                                <i class="bi bi-eye me-1"></i> <?= $noticia['vistas'] ?>
                            </span>
                            <span title="Tiempo Total de Visualización">
                                <i class="bi bi-clock me-1"></i> <?= number_format($noticia['tiempo_total_stats'], 0) ?>s
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
let globalChartVistasInstance = null;
let globalChartTiempoInstance = null;

document.addEventListener("DOMContentLoaded", function() {
    // Cargar estadísticas globales al inicio
    loadGlobalStats();
});

function loadGlobalStats() {
    const fechaInicio = document.getElementById('filterFechaInicio').value;
    const fechaFin = document.getElementById('filterFechaFin').value;
    const categoria = document.getElementById('filterCategoria').value;

    const params = new URLSearchParams({
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        categoria: categoria
    });

    fetch(`./../controllers/obtener_estadisticas_globales.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) throw new Error("Error en la respuesta del servidor");
            return response.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            renderGlobalCharts(data.labels, data.vistas, data.tiempo);
        })
        .catch(err => {
            console.error("Error cargando estadísticas globales:", err);
            // alert("Error al cargar estadísticas: " + err.message); // Descomentar para debug visual
        });
}

function renderGlobalCharts(labels, dataVistas, dataTiempo) {
    // Gráfica de Vistas
    const ctxVistas = document.getElementById('globalChartVistas').getContext('2d');
    
    if (globalChartVistasInstance) {
        globalChartVistasInstance.destroy();
    }

    globalChartVistasInstance = new Chart(ctxVistas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Vistas en Periodo',
                data: dataVistas,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfica de Tiempo
    const ctxTiempo = document.getElementById('globalChartTiempo').getContext('2d');

    if (globalChartTiempoInstance) {
        globalChartTiempoInstance.destroy();
    }

    globalChartTiempoInstance = new Chart(ctxTiempo, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tiempo Total (s)',
                data: dataTiempo,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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