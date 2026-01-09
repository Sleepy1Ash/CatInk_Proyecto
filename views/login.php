<?php
include("./../layout/header.php");
?>
<div class="container-fluid">
    <center>
        <div class="card bg-transparent border-0 fs-3 text-white" style="width: 22rem;">
            <img src="./../../CatInk_Proyecto/img/logo_alt.jpg" class="card-img-top">
            <div class="card-header">
                <h5 class="card-title">Login</h5>
            </div>
            <form action="./../../CatInk_Proyecto/controllers/logincontroller.php" method="POST">
                <div class="card-body" style="height: 18rem; margin-top: 4px; margin-bottom: 4px;">
                    <div class="row">
                        <label class="form-label" for="">Usuario</label>
                        <input type="text" class="form-control" name="usuario">
                        <label class="form-label" for="">Contraseña</label>
                        <input type="password" class="form-control" name="pass">
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn"  type="submit" margin-left>Iniciar</button>
                </div>
            </form>
        </div>
    </center>
    
</div>
<?php
include("./../layout/footer.php");
?>