<?php 
// Página de administración (estadisticas)
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
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
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 50px;">ID</th>
                            <th scope="col" style="width: 80px;">Imagen</th>
                            <th scope="col">Título</th>
                            <th scope="col">Descripción</th>
                            <th scope="col" style="width: 150px;">Fecha</th>
                            <th scope="col" style="width: 100px;" class="text-center">Vistas</th>
                            <th scope="col" style="width: 150px;" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Consulta para obtener noticias ordenadas por fecha descendente
                        $sql = "SELECT id, titulo, descripcion, fecha_publicacion, vistas, crop3 FROM noticias ORDER BY fecha_publicacion DESC";
                        $result = $con->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $cropImg = !empty($row['crop3']) ? "./../" . $row['crop3'] : "https://via.placeholder.com/60";
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($cropImg) ?>" alt="Thumb" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($row['titulo']) ?></td>
                            <td><?= htmlspecialchars(substr($row['descripcion'], 0, 60)) . (strlen($row['descripcion']) > 60 ? '...' : '') ?></td>
                            <td><?= date("d/m/Y", strtotime($row['fecha_publicacion'])) ?></td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark rounded-pill"><?= number_format($row['vistas']) ?></span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="editar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <!-- Botón para abrir modal de eliminación -->
                                    <button class="btn btn-danger btn-delete" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-titulo="<?= htmlspecialchars($row['titulo']) ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No hay noticias registradas.</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
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