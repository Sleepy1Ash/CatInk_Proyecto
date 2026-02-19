<?php
    include("./../layout/headerAdmin.php");
    include("./../data/conexion.php");
    $ACL = $_SESSION['ACL']['correos'] ?? [
        "crear" => false,
        "leer" => false,
        "editar" => false,
        "eliminar" => false
    ];
?>
<script>
    const ACL = <?= json_encode($ACL) ?>;
</script>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Corros Publicitarios</h1>
    </div>
    <?php if ($ACL['crear']): ?>
        <div class="col">
            <a href="correo_pub.php" class="btn btn-success"><i class="bi bi-plus-lg"></i>Agregar Correo Publicitario</a>
        </div>
    <?php endif; ?>
</div>
<?php include("./../layout/footerAdmin.php") ?>