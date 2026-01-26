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
                    <div class="d-flex justify-content-end mb-3">
                        <div class="btn-group" role="group" aria-label="Rango de tiempo">
                            <button type="button" class="btn btn-outline-secondary btn-sm active" onclick="updateCharts(<?= $noticiaId ?>, 'diario', this)">Diario</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateCharts(<?= $noticiaId ?>, 'semanal', this)">Semanal</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateCharts(<?= $noticiaId ?>, 'mensual', this)">Mensual</button>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Gráfica 1: Lecturas (Vistas) -->
                        <div class="col-md-6">
                            <h6>Lecturas (Vistas)</h6>
                            <div style="height: 200px;">
                                <canvas id="chartVistas_<?= $noticiaId ?>"></canvas>
                            </div>
                        </div>
                        <!-- Gráfica 2: Tiempo de Visualización -->
                        <div class="col-md-6">
                            <h6>Tiempo de Visualización (Segundos)</h6>
                            <div style="height: 200px;">
                                <canvas id="chartTiempo_<?= $noticiaId ?>"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
</div>
<script>
// Almacén global para las instancias de gráficos para poder destruirlos antes de actualizar
const chartInstances = {};

document.addEventListener("DOMContentLoaded", function () {
    updateCharts(<?= $noticiaId ?>, 'diario');
});
function updateCharts(noticiaId, rango, btnElement = null) {
    // Actualizar estado de botones si se hizo click
    if (btnElement) {
        const group = btnElement.parentElement;
        group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
        btnElement.classList.add('active');
    }

    // Llamada API
    fetch(`../controllers/obtener_estadisticas.php?noticia_id=${noticiaId}&rango=${rango}`)
        .then(response => response.json())
        .then(data => {
            renderVistasChart(noticiaId, data.labels, data.vistas);
            renderTiempoChart(noticiaId, data.labels, data.tiempoPromedio, data.tiempoTotal);
        })
        .catch(err => console.error("Error cargando estadísticas:", err));
}

function renderVistasChart(id, labels, dataVistas) {
    const ctx = document.getElementById(`chartVistas_${id}`).getContext('2d');
    const chartId = `vistas_${id}`;

    // Destruir si existe
    if (chartInstances[chartId]) {
        chartInstances[chartId].destroy();
    }

    chartInstances[chartId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Cantidad de Lecturas',
                data: dataVistas,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
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

function renderTiempoChart(id, labels, dataPromedio, dataTotal) {
    const ctx = document.getElementById(`chartTiempo_${id}`).getContext('2d');
    const chartId = `tiempo_${id}`;

    if (chartInstances[chartId]) {
        chartInstances[chartId].destroy();
    }

    chartInstances[chartId] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Promedio (s)',
                    data: dataPromedio,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y',
                    tension: 0.3
                },
                {
                    label: 'Acumulado Total (s)',
                    data: dataTotal,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y1',
                    tension: 0.3,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Promedio' },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Total Acumulado' },
                    grid: { drawOnChartArea: false }, // para no ensuciar la grilla
                    beginAtZero: true
                }
            }
        }
    });
}
</script>
<?php
// Se incluye el footerAdmin que cierra el main y añade scripts
include("./../layout/footerAdmin.php");
?>>