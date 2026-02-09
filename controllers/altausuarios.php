<?php
    include("./../data/conexion.php");
    header('Content-Type: application/json');
    if (!isset($_POST['usuario']) || !isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['crop'])) {
        echo json_encode(['error' => 'Faltan datos']);
        exit;
    }
    $nombre = trim($_POST['nombre']);
    $usuario = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $passConfirm = trim($_POST['confirm_password']);
    if ($password !== $passConfirm) {
        echo json_encode(['error' => 'Las contraseñas no coinciden']);
        exit;
    }
    $stmt = $con->prepare("SELECT id_u FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $error = 'El nombre de usuario ya existe';
    } else {
        $alt = $con->prepare("INSERT INTO usuarios (nombre, usuario, email, pass) VALUES (?, ?, ?, ?)");
        $alt->bind_param("ssss", $nombre, $usuario, $email, $password);
        $alt->execute();
        $error = 'Usuario registrado con éxito';
    }
