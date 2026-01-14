<?php 
// Página de creacion (añadir noticias)
include("./../layout/headerAdmin.php");
?>
<div class="admin-container">
    <title>Alta de noticia | CatInk News</title>
    <form id="formPublicacion" action="./../../CatInk_Proyecto/controllers/noticiascontroller.php" method="POST" enctype="multipart/form-data">
        <div class="form-card card">
            <!-- TÍTULO -->
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <!-- DESCRIPCIÓN -->
            <div class="form-group">
                <label for="descripcion">Descripción corta</label>
                <textarea 
                    id="descripcion" 
                    name="descripcion" 
                    rows="3"
                    placeholder="Resumen breve de la noticia"
                    required></textarea>
            </div>
            <!-- IMAGEN PRINCIPAL (placeholder para Parte 2) -->
            <div class="form-group">
                <label>Imagen principal</label>

                <!-- Subida -->
                <input type="file" id="imageInput" accept="image/*">

                <!-- Selector de formato -->
                <div class="aspect-ratio-controls">
                    <button type="button" class="btn btn-outline-secondary" data-ratio="16/9">16:9</button>
                    <button type="button" class="btn btn-outline-secondary" data-ratio="21/6">21:6</button>
                    <button type="button" class="btn btn-outline-secondary" data-ratio="1">1:1</button>
                    <button type="button" class="btn btn-outline-secondary" data-ratio="4/5">4:5</button>
                </div>

                <!-- Zona cropper -->
                <div class="cropper-container">
                    <img id="cropperImage">
                </div>

                <!-- Acciones -->
                <div class="crop-actions">
                    <button type="button" class="btn btn-outline-secondary" id="cropAdd">➕ Añadir recorte</button>
                    <button type="button" class="btn btn-outline-secondary" id="cropReset">Reset</button>
                </div>

                <!-- Recortes finales -->
                <div class="cropped-preview">
                    <h4>Vista previa</h4>
                    <div class="preview-grid" id="previewGrid"></div>
                </div>

                <!-- Inputs ocultos -->
                <input type="hidden" name="crop1" id="crop1">
                <input type="hidden" name="crop2" id="crop2">
                <input type="hidden" name="crop3" id="crop3">
            </div>

            <!-- CONTENIDO (placeholder para Parte 3) -->
           <div class="form-group">
                <label>Contenido</label>

                <!-- Toolbar personalizada -->
                <div id="editorToolbar">
                    <select class="ql-font"></select>
                    <select class="ql-size"></select>

                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>

                    <button class="ql-link"></button>
                    <button class="ql-image"></button>

                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>

                    <button class="ql-clean"></button>
                </div>

                <div id="editorContent"></div>
                <!-- Campo oculto para PHP -->
                <input type="hidden" name="contenido" id="contenido">
            </div>

            <!-- PROGRAMACIÓN (placeholder para Parte 4) -->
            <div class="form-group">
                <label>Estado de publicación</label>
                <select name="estado" id="estadoPublicacion" required>
                    <option value="borrador">Borrador</option>
                    <option value="publicado">Publicar ahora</option>
                    <option value="programado">Programar publicación</option>
                </select>
            </div>

            <div class="form-group" id="programacionBox" style="display:none;">
                <label>Fecha y hora de publicación</label>
                <input 
                    type="datetime-local" 
                    name="fecha_publicacion" 
                    id="fechaPublicacion">
            </div>
            <!-- ACCIONES -->
            <div class="form-actions">
                <button type="submit" class="btn-success">
                    Guardar noticia
                </button>
            </div>
        </div>
    </form>
</div>
<div id="cropModal" class="crop-modal" role="dialog" aria-modal="true">
    <div class="crop-modal-content">
        <h4>Recortar imagen</h4>

        <div class="cropper-container-editor">
            <img id="cropImageEditor">
        </div>

        <div class="crop-actions">
            <select id="cropAspectEditor">
                <option value="free">Libre</option>
                <option value="16/9">16:9</option>
                <option value="21/9">21:9</option>
                <option value="1/1">1:1</option>
            </select>

            <button id="cropConfirmEditor" class="btn-success">Aplicar</button>
            <button id="cropCancelEditor" class="btn-secondary">Cancelar</button>
        </div>
    </div>
</div>
<?php
// Se incluye el footerAdmin que cierra el main y añade scripts
include("./../layout/footerAdmin.php");
?>