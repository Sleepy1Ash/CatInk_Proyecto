<?php 
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
// Inicializamos array para la semana
$newsByDate = [];
$period = new DatePeriod(
    $startOfWeek,
    new DateInterval('P1D'),
    (clone $endOfWeek)->modify('+1 day')
);
foreach ($period as $day) {
    $key = $day->format('Y-m-d');
    $newsByDate[$key] = [];
}
// Obtener noticias de la semana
$sql = "SELECT id, titulo, descripcion, fecha_publicacion, vistas, likes, crop3 
        FROM noticias 
        WHERE fecha_publicacion BETWEEN ? AND ?
        ORDER BY fecha_publicacion ASC";  
$stmt = $con->prepare($sql);
$stmt->bind_param("ss", $fechaInicio, $fechaFin);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $dateKey = date('Y-m-d', strtotime($row['fecha_publicacion']));
    $newsByDate[$dateKey][] = $row;
}
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Contenidos</h1>
    </div>
    <a href="crear.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Nueva Noticia</a>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'eliminado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Noticia eliminada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="calendar-days">
                <?php foreach ($newsByDate as $date => $newsList): ?>
                    <div class="day-column">
                        <div class="day-header"><?= date("d/m/Y", strtotime($date)) ?></div>
                        <?php if (empty($newsList)): ?>
                            <div class="text-muted text-center py-4">Sin publicaciones</div>
                        <?php else: ?>
                            <div class="day-news">
                                <?php foreach ($newsList as $row): 
                                    $img = !empty($row['crop3']) ? "./../".$row['crop3'] : "https://via.placeholder.com/300x200";
                                    $ahora = new DateTime();
                                    $fechaPublicacion = new DateTime($row['fecha_publicacion']);
                                    if ($fechaPublicacion < $ahora) {
                                        $estado = '<span><i class="bi bi-check-circle"></i> Publicado</span>';
                                    } elseif ($fechaPublicacion->format('Y-m-d') === $ahora->format('Y-m-d') && $fechaPublicacion > $ahora) {
                                        $estado = '<span><i class="bi bi-calendar-event-fill"></i> Por publicar</span>';
                                    } else {
                                        $estado = '<span><i class="bi bi-calendar-event"></i> Programado</span>';
                                    }
                                ?>
                                    <div class="noticias-card">
                                        <div class="card-header d-flex justify-content-between">
                                            <?= $estado ?>
                                            <span><i class="bi bi-clock"></i> <?= $fechaPublicacion->format('H:i') ?></span>
                                        </div>
                                        <img src="<?= htmlspecialchars($img) ?>" alt="" class="card-img-top">
                                        <h6><?= htmlspecialchars($row['titulo']) ?></h6>
                                        <small class="text-muted">
                                            👁 <?= number_format($row['vistas']) ?> | ❤️ <?= number_format($row['likes']) ?>
                                        </small>
                                        <div class="noticias-actions">
                                            <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-edit" title="Editar"><i class="bi bi-pencil-square"></i></a>
                                            <a href="see.php?id=<?= $row['id'] ?>" class="btn btn-view" title="Ver Estadisticas"><i class="bi bi-eye"></i></a>
                                            <button class="btn btn-delete" data-id="<?= $row['id'] ?>" data-titulo="<?= htmlspecialchars($row['titulo']) ?>" title="Eliminar"><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="botonesSemana d-flex justify-content-between align-items-center mt-4">
                <a href="?week=<?= $weekOffset - 1 ?>" class="btn btn-outline-secondary">← Semana anterior</a>
                <h4>Semana del <?= $startOfWeek->format('d/m') ?> al <?= $endOfWeek->format('d/m/Y') ?></h4>
                <a href="?week=<?= $weekOffset + 1 ?>" class="btn btn-outline-secondary">Semana siguiente →</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal de Confirmación para Eliminar -->
<div id="modalOverlay" class="crop-modal" style="display: none;">
    <div class="crop-modal-content">
        <h3 id="modalTitle">Confirmar eliminación</h3>
        <p>¿Estás seguro de que deseas eliminar esta noticia? Esta acción no se puede deshacer.</p>
        <form id="modalForm" action="../controllers/eliminar_noticia.php" method="POST">
            <input type="hidden" name="id" id="modalId">
            <div class="crop-actions">
                <button type="button" class="btn btn-secondary btn-cancel">Cancelar</button>
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </div>
        </form>
    </div>
</div>
<?php include("./../layout/footerAdmin.php"); ?>