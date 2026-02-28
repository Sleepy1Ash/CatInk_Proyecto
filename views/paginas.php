<?php
    include("./../layout/headerAdmin.php");
    include("./../data/conexion.php");
    $sql = "SELECT * FROM paginas"; // Asumiendo que 'Nosotros' tiene id=1 y 'Términos y Condiciones' id=2
    $result = $con->query($sql);
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Páginas</h1>
    </div>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Página actualizada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Sección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nombre_pag']) ?></td>
                            <td>
                                <button 
                                    class="btn btn-secondary btnEditar" 
                                    data-id="<?= $row['id_pag'] ?>" 
                                    data-nombre="<?= $row['nombre_pag'] ?>"
                                    data-contenido="<?= base64_encode($row['contenido_pag']) ?>">
                                    <i class="bi bi-pencil-square"></i>Editar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modalPagina" class="modal">
    <form id="formPagina" class="modal-content-nativo-special" action="./../controllers/pagina.php" method="POST">
        <div class="form-card">
            <span id="modalClose" class="modal-close">&times;</span>
            <div class="form-group">
                <input type="hidden" name="id" id="pagina_id">
                <label for="nombre">Sección</label>
                <select name="nombre" id="nombre">
                    <option value="nosotros" selected>Nosotros</option>
                    <option value="terminos">Términos y condiciones</option>
                </select>
            </div>
            <div class="form-group">
                <label>Contenido</label>
                <div id="editorpag" class="editor-content"></div>
                <!-- AQUI SE GUARDA QUILL -->
                <input type="hidden" name="contenido" id="contenido">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </form>
</div>
<script>
    var quillpag = new Quill('#editorpag', {
        theme: 'snow'
    });
    const modalPagina = document.getElementById("modalPagina");
    document.querySelectorAll(".btnEditar").forEach(btn => {
        btn.addEventListener("click", function(){
            document.getElementById("pagina_id").value =
            this.dataset.id;
            document.getElementById("nombre").value =
            this.dataset.nombre;
            let contenido = atob(this.dataset.contenido);
            modalPagina.style.display = "block";
            setTimeout(() => {
                quillpag.setContents([]);
                quillpag.clipboard.dangerouslyPasteHTML(contenido);
            }, 100);
        });
    });
    document.getElementById("formPagina")
    .addEventListener("submit", function(){
        document.getElementById("contenido").value =
        quillpag.root.innerHTML;
    });
    modalClose.addEventListener('click', () => {
        modalPagina.style.display = "none";
        modalNombre.parentElement.style.display = "block"; // reset
    });
    modal.addEventListener('click', (e) => {
        if(e.target === modal) {
            modalPagina.style.display = "none";
            modalNombre.parentElement.style.display = "block";
        }
    });
</script>
<?php include("./../layout/footerAdmin.php"); ?>