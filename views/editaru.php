<?php
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
$id = $_GET['id'];
$stmt = $con->prepare("SELECT * FROM usuarios WHERE id_u=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Usuario</h1>
    </div>
    <form id="editUserForm" action="./../controllers/editarusuario.php?id=<?= $id ?>" method="POST">
        <div class="form-card card">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= $user['nombre'] ?>" required>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <input type="text" name="usuario" value="<?= $user['usuario'] ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= $user['correo'] ?>" required>
            </div>
            <!-- CONTRASEÑA OPCIONAL -->
            <div class="form-group">
                <label>Nueva Contraseña (opcional)</label>
                <input type="password" name="password">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password">
            </div>
            <div class="form-group">
                <div class="row">
                    <?php
                        function check($perm,$bit){ return ($perm & $bit) ? "checked" : ""; }
                    ?>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Permisos Publicidad</h5>
                            </div>
                            <div class="card-body form-group">
                                <label><input type="checkbox" name="publicidad[]" value="1" <?= check($user['perm_publicidad'],1) ?>> Crear</label>
                                <label><input type="checkbox" name="publicidad[]" value="2" <?= check($user['perm_publicidad'],2) ?>> Ver</label>
                                <label><input type="checkbox" name="publicidad[]" value="4" <?= check($user['perm_publicidad'],4) ?>> Editar</label>
                                <label><input type="checkbox" name="publicidad[]" value="8" <?= check($user['perm_publicidad'],8) ?>> Eliminar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Permisos Noticias</h5>
                            </div>
                            <div class="card-body form-group">
                                <label><input type="checkbox" name="noticias[]" value="1" <?= check($user['perm_noticias'],1) ?>> Crear</label>
                                <label><input type="checkbox" name="noticias[]" value="2" <?= check($user['perm_noticias'],2) ?>> Ver</label>
                                <label><input type="checkbox" name="noticias[]" value="4" <?= check($user['perm_noticias'],4) ?>> Editar</label>
                                <label><input type="checkbox" name="noticias[]" value="8" <?= check($user['perm_noticias'],8) ?>> Eliminar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Permisos Categorías</h5>
                            </div>
                            <div class="card-body form-group">
                                <label><input type="checkbox" name="categorias[]" value="1" <?= check($user['perm_categorias'],1) ?>> Crear</label>
                                <label><input type="checkbox" name="categorias[]" value="2" <?= check($user['perm_categorias'],2) ?>> Ver</label>
                                <label><input type="checkbox" name="categorias[]" value="4" <?= check($user['perm_categorias'],4) ?>> Editar</label>
                                <label><input type="checkbox" name="categorias[]" value="8" <?= check($user['perm_categorias'],8) ?>> Eliminar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col">  
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Permisos Suscripciones</h5>
                            </div>
                            <div class="card-body form-group">
                                <label><input type="checkbox" name="suscripciones[]" value="1" <?= check($user['perm_suscripciones'],1) ?>> Crear</label>
                                <label><input type="checkbox" name="suscripciones[]" value="2" <?= check($user['perm_suscripciones'],2) ?>> Ver</label>
                                <label><input type="checkbox" name="suscripciones[]" value="4" <?= check($user['perm_suscripciones'],4) ?>> Editar</label>
                                <label><input type="checkbox" name="suscripciones[]" value="8" <?= check($user['perm_suscripciones'],8) ?>> Eliminar</label>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Permisos Usuarios</h5>
                            </div>
                            <div class="card-body form-group">
                                <label><input type="checkbox" name="usuarios[]" value="1" <?= check($user['perm_usuarios'],1) ?>> Crear</label>
                                <label><input type="checkbox" name="usuarios[]" value="2" <?= check($user['perm_usuarios'],2) ?>> Ver</label>
                                <label><input type="checkbox" name="usuarios[]" value="4" <?= check($user['perm_usuarios'],4) ?>> Editar</label>
                                <label><input type="checkbox" name="usuarios[]" value="8" <?= check($user['perm_usuarios'],8) ?>> Eliminar</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Actualizar Usuario</button>
            </div>
        </div>
    </form>
</div>
<?php include("./../layout/footerAdmin.php"); ?>
