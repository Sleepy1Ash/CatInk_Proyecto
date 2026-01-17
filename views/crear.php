<?php 
// Página de creacion (añadir noticias)
include("./../layout/headerAdmin.php");
?>
<div class="admin-container">
    <h1>Alta de noticia | CatInk News</h1>
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
                <input type="file" id="imageInputMain" accept="image/*">
                <br>

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

                <!-- TOOLBAR -->
                <div class="editor-toolbar ql-toolbar ql-snow">
                    <!-- Fuente -->
                    <select class="ql-font">
                        <option value="arial" selected>Arial</option>
                        <option value="times">Times New Roman</option>
                        <option value="roboto">Roboto</option>
                        <option value="courier">Courier</option>
                    </select>

                    <!-- Tamaño -->
                    <select class="ql-size">
                        <option value="small">Pequeño</option>
                        <option selected>Normal</option>
                        <option value="large">Grande</option>
                        <option value="huge">Muy grande</option>
                    </select>

                    <!-- Estilos -->
                    <button class="ql-bold"></button>
                    <button class="ql-italic"></button>
                    <button class="ql-underline"></button>
                    <button class="ql-strike"></button>

                    <!-- Color -->
                    <select class="ql-color"></select>
                    <select class="ql-background"></select>

                    <!-- Alineación -->
                    <select class="ql-align"></select>

                    <!-- Listas -->
                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>

                    <!-- Sangría -->
                    <button class="ql-indent" value="-1"></button>
                    <button class="ql-indent" value="+1"></button>

                    <!-- Enlaces / multimedia -->
                    <button class="ql-link"></button>
                    <button class="ql-image"></button>
                    <button class="ql-video"></button>

                    <!-- Limpiar formato -->
                    <button class="ql-clean"></button>
                </div>

                <!-- EDITOR -->
                <div id="editor" class="editor-content"></div>

                <!-- INPUT OCULTO PARA IMÁGENES -->
                <input type="file" id="imageInputEditor" accept="image/*" hidden>


                <div id="editorContent"></div>
                <!-- Campo oculto para PHP -->
                <input type="hidden" name="contenido" id="contenido">
            </div>

            <!-- PROGRAMACIÓN (placeholder para Parte 4) -->
            <div class="form-group">
                <label>Programar publicación</label>
                <input type="Date" name="fecha" class="btn-calendar">
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