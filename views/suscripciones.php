<?php 
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
$sql = "SELECT * FROM suscripciones";
$resultado = mysqli_query($con, $sql);
$suscripciones = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Suscripciones</h1>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Sexo</th>
                    <th>Fecha de alta</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($suscripciones as $suscripcion): ?>
                <tr>
                    <td><?php echo $suscripcion['nombre_completo']; ?></td>
                    <td><?php echo $suscripcion['correo']; ?></td>
                    <td><?php echo $suscripcion['sexo']; ?></td>
                    <td><?php echo $suscripcion['fecha']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include("./../layout/footerAdmin.php"); ?>