<?php
include("./../layout/header.php");
include("./../data/conexion.php");
$sql = "SELECT contenido_pag FROM paginas WHERE nombre_pag='terminos'";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "No se encontró el contenido de 'Términos y Condiciones'.";
}
?>
<div class="container-fluid">
    <h2>Terminos y condiciones</h2>
    <?php echo $row['contenido_pag']; ?>
</div>
<?php
include("./../layout/footer.php");
?>