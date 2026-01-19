<?php 
// Página de administración (contenido principal de administración)
include("./../layout/headerAdmin.php");
?>
<h1>Panel de Administración - Estadísticas</h1>
<div class="container-fluid">
<?php
    // Obtener todas las noticias
    $sqlNoticias = $con->prepare("SELECT * FROM noticias ORDER BY fecha_publicacion DESC");
    $sqlNoticias->execute();
    $resultNoticias = $sqlNoticias->get_result();

    // Iteramos por cada noticia
    while ($noticia = $resultNoticias->fetch_assoc()) {
        $noticiaId = $noticia['id'];
        $cardId = "news-card-" . $noticiaId;
?>
<div class="card mb-5 shadow-sm" id="<?= $cardId ?>">
    <div class="row g-0">
        <!-- Imagen y Datos Básicos -->
        <div class="col-md-7">
            <div class="row">
                <div class="col">
                    <img src="./../<?= htmlspecialchars($noticia['crop3'] ?? 'img/placeholder.jpg') ?>" class="img-fluid rounded-start" alt="..." style="width: 100%; height: 250px; object-fit: cover;">
                </div>
                <div class="col">
                    <h5 class="card-title"><?= htmlspecialchars($noticia['titulo']) ?></h5>
                    <span class="badge bg-primary">Vistas Totales (Global): <?= $noticia['vistas'] ?></span>
                    <p class="card-text text-muted mt-2"><small><?= htmlspecialchars(substr($noticia['descripcion'], 0, 100)) ?>...</small></p>
                </div>
            </div>
        </div>
        
        <!-- Sección de Gráficas -->
        <div class="col-md-5">
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
<?php
    }
?>
</div>

<script>
// Almacén global para las instancias de gráficos para poder destruirlos antes de actualizar
const chartInstances = {};

document.addEventListener("DOMContentLoaded", function() {
    // Cargar datos iniciales (diario) para todas las tarjetas
    <?php 
    // Reiniciamos el puntero para volver a iterar o simplemente usamos un selector JS
    // Lo más limpio es buscar todos los botones "active" y disparar su evento, 
    // o llamar a la función para cada ID que generamos.
    $sqlNoticias->data_seek(0); 
    while ($n = $resultNoticias->fetch_assoc()) {
        echo "updateCharts({$n['id']}, 'diario');\n";
    }
    ?>
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
?>