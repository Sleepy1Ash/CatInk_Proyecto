<?php
    include("./../layout/headerAdmin.php");
    include("./../data/conexion.php");
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Crear Usuario</h1>
    </div>
    <form id="formUsuario" action="" method="POST" enctype="multipart/form-data">
        <div class="form-card card">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <span>Nombre completo</span>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <span>Nombre de usuario</span>
                <input type="text" id="usuario" name="usuario" required>
                <small id="usuarioEstado"></small>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <span>Correo electrónico</span>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <span>Contraseña</span>
                <input type="password" id="password" name="password" required>
                <span>Confirma contraseña</span>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <small id="errorPassword" style="color:#dc3545; display:none;">
                    Las contraseñas no coinciden
                </small>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">Crear Usuario</button>
            </div>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('formUsuario');
    const inputUsuario = document.getElementById('usuario');
    const estado = document.getElementById('usuarioEstado');

    const passInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const errorPassword = document.getElementById('errorPassword');

    let usuarioValido = false;

    // Validación usuario en tiempo real
    inputUsuario.addEventListener('keyup', () => {
        const usuario = inputUsuario.value.trim();

        if (usuario.length < 3) {
            estado.textContent = 'El usuario debe tener al menos 3 caracteres';
            estado.style.color = '#ffc107';
            usuarioValido = false;
            return;
        }

        fetch(`./../controllers/validar_usuario.php?usuario=${usuario}`)
            .then(res => res.json())
            .then(data => {
                if (data.existe) {
                    estado.textContent = '❌ Usuario no disponible';
                    estado.style.color = '#dc3545';
                    usuarioValido = false;
                } else {
                    estado.textContent = '✅ Usuario disponible';
                    estado.style.color = '#198754';
                    usuarioValido = true;
                }
            })
            .catch(() => {
                estado.textContent = 'Error al validar usuario';
                estado.style.color = '#dc3545';
                usuarioValido = false;
            });
    });

    // Validación final al enviar
    form.addEventListener('submit', (e) => {

        let valido = true;

        // Contraseñas
        if (passInput.value !== confirmInput.value) {
            errorPassword.style.display = 'block';
            valido = false;
        } else {
            errorPassword.style.display = 'none';
        }

        // Usuario
        if (!usuarioValido) {
            alert('El nombre de usuario no es válido o ya existe');
            valido = false;
        }

        if (!valido) {
            e.preventDefault();
        }
    });

});
</script>
<?php include("./../layout/footerAdmin.php"); ?>