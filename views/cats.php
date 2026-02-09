<?php
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");

// Obtener todas las categorías y conteo de noticias
$sql = "
SELECT c.id_c, c.nombre, COUNT(DISTINCT nc.noticia_id) AS total_noticias
FROM categorias c
LEFT JOIN noticia_categoria nc ON c.id_c = nc.categoria_id
GROUP BY c.id_c, c.nombre
ORDER BY c.nombre
";
$result = $con->query($sql);
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Categorias</h1>
    </div>
    <button id="btnCrear" class="btn btn-success"><i class="bi bi-plus-lg"></i> Crear categoría</button>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Lista de Categorías</h5>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Total Noticias</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['nombre']) ?></td>
                        <td><?= $row['total_noticias'] ?></td>
                        <td>
                            <button class="btn btn-secondary btn-editar" 
                                data-id="<?= $row['id_c'] ?>" 
                                data-nombre="<?= htmlspecialchars($row['nombre']) ?>"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-delete btn-eliminar"
                                data-id="<?= $row['id_c'] ?>" 
                                data-nombre="<?= htmlspecialchars($row['nombre']) ?>"><i class="bi bi-trash3"></i></button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- MODAL NATIVO -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span id="modalClose" class="modal-close">&times;</span>
        <h3 id="modalTitle"></h3>
        <br>
        <form id="modalForm">
            <input type="hidden" name="id_c" id="modalId">
            <div class="mb-3">
                <label for="modalNombre">Nombre</label>
                <input type="text" id="modalNombre" name="nombre" required>
            </div>
            <button type="submit" id="modalSubmit" class="btn btn-success"></button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalForm = document.getElementById('modalForm');
    const modalId = document.getElementById('modalId');
    const modalNombre = document.getElementById('modalNombre');
    const modalSubmit = document.getElementById('modalSubmit');
    const modalClose = document.getElementById('modalClose');
    // Abrir modal de crear
    document.getElementById('btnCrear').addEventListener('click', () => {
        modalTitle.textContent = "Crear Categoría";
        modalSubmit.textContent = "Crear";
        modalForm.dataset.action = "crear";
        modalId.value = "";
        modalNombre.value = "";
        modalNombre.parentElement.style.display = "block"; // reset
        modal.style.display = "flex";
    });
    // Abrir modal de editar
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = "Editar Categoría";
            modalSubmit.textContent = "Actualizar";
            modalForm.dataset.action = "editar";
            modalId.value = btn.dataset.id;
            modalNombre.value = btn.dataset.nombre;
            modalNombre.parentElement.style.display = "block"; // reset
            modal.style.display = "flex";
        });
    });
    // Abrir modal de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', () => {
            modalTitle.textContent = "Eliminar Categoría";
            modalSubmit.textContent = "Eliminar";
            modalForm.dataset.action = "eliminar";
            modalId.value = btn.dataset.id;
            modalNombre.value = btn.dataset.nombre;
            modalNombre.parentElement.style.display = "none"; // ocultar input
            modal.style.display = "flex";
        });
    });
    // Cerrar modal
    modalClose.addEventListener('click', () => {
        modal.style.display = "none";
        modalNombre.parentElement.style.display = "block"; // reset
    });
    // Enviar formulario
    modalForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const action = modalForm.dataset.action;
        const formData = new FormData(modalForm);
        let url = "";
        if(action === "crear") url = "./../controllers/crearc.php";
        if(action === "editar") url = "./../controllers/editarc.php";
        if(action === "eliminar") url = "./../controllers/eliminarc.php";
        fetch(url, { method: "POST", body: formData })
            .then(r => {
                if (!r.ok) throw new Error("Error HTTP");
                return r.json();
            })
            .then(d => {
                console.log(d); // 👈 clave
                if (d.success) location.reload();
                else alert(d.error || "Ocurrió un error");
            })
            .catch(err => {
                console.error(err);
                alert("Error en la petición");
            });
    });
    // Cerrar modal al hacer click fuera del contenido
    modal.addEventListener('click', (e) => {
        if(e.target === modal) {
            modal.style.display = "none";
            modalNombre.parentElement.style.display = "block";
        }
    });
});
</script>
<?php include("./../layout/footerAdmin.php"); ?>
