<?php
include("./../layout/header.php");
?>
<!-- Página de login: tarjeta centrada con formulario de autenticación -->
<div class="container-fluid">
    <div style="display:flex; justify-content:center;">
        <div class="card" style="width: 22rem; background:transparent; border:0;">
            <!-- Logo dentro de la tarjeta -->
            <img src="./../../CatInk_Proyecto/img/logo_alt.jpg" class="card-img-top" alt="logo">
            <div class="card-header">
                <h5 class="card-title">Login</h5>
            </div>
            <!-- Formulario de inicio de sesión: action apunta al controlador -->
            <form action="./../../CatInk_Proyecto/controllers/logincontroller.php" method="POST">
                <div class="card-body" style="height: 18rem; margin-top: 4px; margin-bottom: 4px;">
                    <div class="row">
                        <label for="">Usuario</label>
                        <input type="text" class="input" name="usuario">
                        <label for="">Contraseña</label>
                        <input type="password" class="input" name="pass">
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn"  type="submit">Iniciar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include("./../layout/footer.php");
?>