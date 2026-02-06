<?php
include("./../layout/header.php");
?>
<div class="container-fluid">
    <div style="display:flex; justify-content:center; margin-top: 5%;">
        <div class="card" style="width: 22rem; background:transparent; border:0;">
            <!-- Logo dentro de la tarjeta -->
            <img src="./../img/logo_alt.jpg" class="card-img-top" alt="logo">
            <div class="card-header text-center">
                <h5 class="card-title">Login</h5>
            </div>
            <!-- Formulario de inicio de sesión -->
            <form action="./../controllers/logincontroller.php" method="POST">
                <div class="card-body" style="height: 18rem; margin-top: 4px; margin-bottom: 4px;">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="pass" name="pass" required>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary w-100" type="submit">Iniciar Sesión</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include("./../layout/footer.php");
?>