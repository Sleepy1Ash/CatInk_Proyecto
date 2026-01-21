<?php 
// Página de creacion (añadir noticias)
include("./../layout/headerAdmin.php");
?>
<div class="admin-container">
    <h1>Alta de noticia | CatInk News</h1>
    <form id="formPublicacion" action="./../../CatInk_Proyecto/controllers/noticiascontroller.php" method="POST" enctype="multipart/form-data">
        <div class="form-card card">
            <!-- Autor oculto -->
            <input type="hidden" name="autor" value="<?php echo $fila['id_u']; ?>">
            <!-- TÍTULO -->
            <div class="form-group">
                <label for="titulo">Título</label>
                <span>Maximo 50 caracteres</span>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            <!-- DESCRIPCIÓN -->
            <div class="form-group">
                <label for="descripcion">Descripción corta </label>
                <span>Maximo 150 caracteres</span>
                <textarea 
                    id="descripcion" 
                    name="descripcion" 
                    rows="3"
                    placeholder="Resumen breve de la noticia"
                    required></textarea>
            </div>
            <!-- Categoria -->
             <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="btn-secondary" required>
                    <option value="">Seleccione una categoria</option>
                    <option value="Peliculas">Peliculas</option>
                    <option value="Series">Series</option>
                    <option value="Cultura Pop">Cultura Pop</option>
                    <option value="Anime">Anime</option>
                </select>
             </div>
            <!-- IMAGEN PRINCIPAL (placeholder para Parte 2) -->
            <div class="form-group">
                <label>Imagen principal</label>
                <span>El orden de los botones muestra las capturas necesarias y como deben ser guardadas</span>
                <!-- Subida -->
                <input type="file" id="imageInputMain" accept="image/*">
                <br>
                <!-- Selector de formato -->
                <div class="aspect-ratio-controls">
                    <button type="button" class="btn btn-outline-secondary" data-ratio="1">Original</button>
                    <button type="button" class="btn btn-outline-secondary" data-ratio="21/6">Baner</button>
                    <button type="button" class="btn btn-outline-secondary" data-ratio="16/9">Miniatura</button>
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

                <!-- TOOLBAR -->
                <div class="editor-toolbar ql-toolbar ql-snow">
                    <!-- Fuente -->
                    <select class="ql-font" title="Fuente">
                        <option value="arial" selected>Arial</option>
                        <option value="times">Times New Roman</option>
                        <option value="roboto">Roboto</option>
                        <option value="courier">Courier</option>
                    </select>

                    <!-- Tamaño -->
                    <select class="ql-size" title="Tamaño">
                        <option value="small">Pequeño</option>
                        <option selected>Normal</option>
                        <option value="large">Grande</option>
                        <option value="huge">Muy grande</option>
                    </select>

                    <!-- Estilos -->
                    <button class="ql-bold" title="Negritas"></button>
                    <button class="ql-italic" title="Cursiva"></button>
                    <button class="ql-underline" title="Subrayado"></button>
                    <button class="ql-strike" title="Tachado"></button>

                    <!-- Color -->
                    <select class="ql-color" title="Color"></select>
                    <select class="ql-background" title="Fondo"></select>

                    <!-- Alineación -->
                    <select class="ql-align" title="Alineación"></select>
                    <!-- Listas -->
                    <button class="ql-list" value="ordered" title="Lista ordenada"></button>
                    <button class="ql-list" value="bullet" title="Lista desordenada"></button>

                    <!-- Sangría -->
                    <button class="ql-indent" value="-1" title="Reducir sangría"></button>
                    <button class="ql-indent" value="+1" title="Aumentar sangría"></button>

                    <!-- Enlaces / multimedia -->
                    <button class="ql-link" title="Añadir link"></button>
                    <button class="ql-image" title="Insertar imagen"></button>
                    <button class="ql-video" title="Insertar video"></button>

                    <!-- Limpiar formato -->
                    <button class="ql-clean" title="Limpiar formato"></button>
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
                <input type="datetime-local" name="fecha_publicacion" class="btn-calendar">
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