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
<?php
    $sql= $con -> prepare("SELECT * FROM correos_publicitarios ORDER BY creado DESC");
    $sql->execute();
    $result = $sql->get_result();
    $correos = $result -> fetch_all(MYSQLI_ASSOC);
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Corros Publicitarios</h1>
    </div>
    <?php if ($ACL['crear']): ?>
        <div class="col">
            <a href="correo_pub.php" class="btn btn-success"><i class="bi bi-plus-lg"></i>Agregar Correo Publicitario</a>
        </div>
    <?php endif; ?>
    <br>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Titulo</th>
                    <th>Contenido</th>
                    <th>Url</th>
                    <th>Enviado</th>
                    <?php if(!empty($ACL['editar']) || !empty($ACL['eliminar'])): ?>
                        <th>Acciones</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($correos as $correo): ?>
                    <tr>
                        <th><?= $correo['titulo'] ?></th>
                        <th><?= $correo['contenido'] ?></th>
                        <th><?= $correo['url_c'] ?></th>
                        <th><?= $correo['envio'] ?></th>
                        <?php if(!empty($ACL['editar']) || !empty($ACL['eliminar'])): ?>
                            <th>
                                <?php if(!empty($ACL['editar'])): ?>
                                    <a href="" class="btn btn-secondary">Editar</a>
                                <?php endif;?>
                                <?php if(!empty($ACL['eliminar'])): ?>
                                    <a href="" class="btn btn-secondary">Eliminar</a>
                                <?php endif;?>
                            </th>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include("./../layout/footerAdmin.php") ?>