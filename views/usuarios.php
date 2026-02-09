<?php
include("./../layout/headerAdmin.php");
include("./../data/conexion.php");
$stmt = $con->prepare("SELECT * FROM usuarios");
$stmt->execute();
$usuarios = $stmt->get_result();
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestión de Usuarios</h1>
    </div>
    <a href="./crearu.php" class="btn btn-success"><i class="bi bi-plus-lg"></i>Crear Usuario</a>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Lista de Usuarios</h5>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Email</th>
                        <th scope="col">Fecha Registro</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id_u'] ?></td>
                        <td><?= $u['nombre'] ?></td>
                        <td><?= $u['usuario'] ?></td>
                        <td><?= $u['correo'] ?></td>
                        <td><?= $u['registro'] ?></td>
                        <td>
                            <a href="" class="btn btn-secondary" title="Editar Usuario"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-delete" title="Eliminar Usuario"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("./../layout/footerAdmin.php"); ?>