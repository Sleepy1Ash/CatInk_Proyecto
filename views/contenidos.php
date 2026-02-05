<?php 
// Página de administración (estadisticas)
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
// Calculo de semanas
$weekOffset = isset($_GET['week']) ? (int)$_GET['week'] : 0;

$startOfWeek = new DateTime();
$startOfWeek->modify(($weekOffset * 7) . ' days');
$startOfWeek->modify('monday this week');

$endOfWeek = clone $startOfWeek;
$endOfWeek->modify('sunday this week');

$fechaInicio = $startOfWeek->format('Y-m-d');
$fechaFin = $endOfWeek->format('Y-m-d');
// Asignacion de dias a la semana
$newsByDate = [];
$period = new DatePeriod(
    $startOfWeek,
    new DateInterval('P1D'),
    (clone $endOfWeek)->modify('+1 day')
);
foreach ($period as $day) {
    $key = $day->format('Y-m-d');
    $newsByDate[$key] = []; // día vacío por defecto
}

?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Contenidos</h1>
        <a href="crear.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nueva Noticia</a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'eliminado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Noticia eliminada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <br>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php
                // Consulta para obtener noticias ordenadas por fecha descendente
                $sql = "SELECT id, titulo, descripcion, fecha_publicacion, vistas, crop3 
                        FROM noticias 
                        WHERE fecha_publicacion BETWEEN '$fechaInicio' AND '$fechaFin'
                        ORDER BY fecha_publicacion ASC ";  
                $result = $con->query($sql);
            ?>
            <?php if ($result->num_rows > 0): ?>
                <?php 
                while ($row = $result->fetch_assoc()){
                    $dateKey = date('Y-m-d', strtotime($row['fecha_publicacion']));
                    $newsByDate[$dateKey][] = $row;
                }
                ?>
                <div class="calendar-days">
                    <?php foreach ($newsByDate as $date => $newsList): ?>
                        <div class="day-column">
                            <div class="day-header">
                                <?= date("d/m/Y", strtotime($date)) ?>
                            </div>
                            <?php if (empty($newsList)): ?>
                                <div class="text-muted text-center py-4">
                                    Sin publicaciones
                                </div>
                            <?php endif; ?>
                            <div class="day-news">
                                <?php foreach ($newsList as $row): 
                                    $img = !empty($row['crop3']) ? "./../".$row['crop3'] : "https://via.placeholder.com/300x200";
                                ?>
                                    <div class="noticias-card">
                                        <div class="card-header">
                                            <?php
                                                date_default_timezone_set('America/Mexico_City'); // ajusta si usas otra zona

                                                $ahora = new DateTime(); 
                                                $fechaPublicacion = new DateTime($row['fecha_publicacion']);

                                                // Extraemos solo la fecha (sin hora) para algunas comparaciones
                                                $fechaHoy = $ahora->format('Y-m-d');
                                                $fechaPub = $fechaPublicacion->format('Y-m-d');

                                                if ($fechaPublicacion < $ahora) {
                                                    // Ya pasó fecha y hora
                                                    echo '<span><i class="bi bi-check-circle"></i> Publicado</span>
                                                    <span><i class="bi bi-clock"></i> ' . $fechaPublicacion->format('H:i') . '</span>
                                                    ';

                                                } elseif ($fechaPub === $fechaHoy && $fechaPublicacion > $ahora) {
                                                    // Hoy, pero más tarde
                                                    echo '<span><i class="bi bi-calendar-event-fill"></i></i> Por publicar</span>
                                                    <span><i class="bi bi-clock"></i> ' . $fechaPublicacion->format('H:i') . '</span>
                                                    ';

                                                } else {
                                                    // Fecha futura
                                                    echo '<span><i class="bi bi-calendar-event"></i> Programado</span>
                                                    <span><i class="bi bi-clock"></i> ' . $fechaPublicacion->format('H:i') . '</span>
                                                    ';
                                                }
                                            ?>
                                        </div>

                                        <img src="<?= htmlspecialchars($img) ?>" alt="" class="card-img-top">
                                        
                                        <h6><?= htmlspecialchars($row['titulo']) ?></h6>

                                        <small class="text-muted">
                                            👁 <?= number_format($row['vistas']) ?>
                                        </small>

                                        <div class="noticias-actions">
                                            <button class="btn btn-edit">
                                                <a href="editar.php?id=<?= $row['id'] ?>">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </button>
                                            <button class="btn-view">
                                                <a href="see.php?id=<?= $row['id'] ?>">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </button>
                                            <button class="btn-delete"
                                                data-id="<?= $row['id'] ?>"
                                                data-titulo="<?= htmlspecialchars($row['titulo']) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    No hay noticias esta semana.
                </div>
            <?php endif; ?>
            <div class="botonesSemana">
                <a href="?week=<?= $weekOffset - 1 ?>" class="btn btn-outline-secondary">
                    ← Semana anterior
                </a>

                <h4>
                    Semana del <?= $startOfWeek->format('d/m') ?> al <?= $endOfWeek->format('d/m/Y') ?>
                </h4>

                <a href="?week=<?= $weekOffset + 1 ?>" class="btn btn-outline-secondary">
                    Semana siguiente →
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar (Requerido por admin.js) -->
<div id="modalOverlay" class="crop-modal" style="display: none;">
    <div class="crop-modal-content">
        <h3 id="modalTitle">Confirmar eliminación</h3>
        <p>¿Estás seguro de que deseas eliminar esta noticia? Esta acción no se puede deshacer.</p>
        
        <form id="modalForm" action="../controllers/eliminar_noticia.php" method="POST">
            <!-- El ID se inyecta vía JS -->
            <input type="hidden" name="id" id="modalId">
            
            <div class="crop-actions">
                <button type="button" class="btn btn-secondary btn-cancel">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>

<?php
// Se incluye el footerAdmin que cierra el main y añade scripts
include("./../layout/footerAdmin.php");
?>