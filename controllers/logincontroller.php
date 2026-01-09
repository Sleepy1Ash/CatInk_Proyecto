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
        $_SESSION['id'] = $fila['id'];
        $rol = $fila['rol'];
        if ($rol === 'Admin') {
            header('Location: ../views/admin.php');
            exit();
        } elseif ($rol === 'user') {
            header('Location: ../views/users/home.php');
            exit();
        }
    } else {
        header('Location: ../index.php?error=1');
        exit();
    }

?>