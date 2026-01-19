<?php
    include("../data/conexion.php");
    $usuario = $_POST['usuario'];
    $pass = $_POST['pass'];
    $sql = "SELECT * FROM usuarios WHERE usuario='$usuario' AND pass='$pass'";
    $result=mysqli_query($con,$sql);
    if ($result && $result->num_rows > 0) {
        session_start();
        $fila = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['id_u'] = $fila['id_u'];
        header('Location: ../views/admin.php');
        exit();
    } else {
        header('Location: ../index.php?error=1');
        exit();
    }

?>