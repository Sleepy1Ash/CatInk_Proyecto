<?php
include("../data/conexion.php");
// ============================
// OBTENER DATOS DEL FORMULARIO
// ============================
$usuario = $_POST['usuario'] ?? '';
$pass = $_POST['pass'] ?? '';
if (empty($usuario) || empty($pass)) {
    header('Location: ../index.php?error=1');
    exit();
}
// ============================
// CONSULTA SEGURA (Prepared Statement)
// ============================
$stmt = $con->prepare("SELECT id_u, usuario, pass FROM usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    $passCorrecta = false;
    // ============================
    // VERIFICAR PASSWORD (plana o hash)
    // ============================
    if ($pass === $fila['pass']) {
        // contraseña en texto plano
        $passCorrecta = true;
    } elseif (password_verify($pass, $fila['pass'])) {
        // contraseña hasheada
        $passCorrecta = true;
    }
    if ($passCorrecta) {
        session_start();
        session_regenerate_id(true); // seguridad
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['id_u'] = $fila['id_u'];
        header('Location: ../views/admin.php');
        exit();
    } else {
        header('Location: ../index.php?error=1');
        exit();
    }
} else {
    // Usuario no encontrado
    header('Location: ../index.php?error=1');
    exit();
}
?>