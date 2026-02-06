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
// ============================
// VERIFICAR PASSWORD
// ============================
// Si todavía tienes passwords en texto plano, usa esto:
if ($result && $result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    // Si ya tienes contraseñas hasheadas, usar:
    // if (password_verify($pass, $fila['pass'])) { ... }
    if ($pass === $fila['pass']) {
        session_start();
        session_regenerate_id(true); // seguridad
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['id_u'] = $fila['id_u'];
        header('Location: ../views/admin.php');
        exit();
    }
}
// ============================
// LOGIN FALLIDO
// ============================
header('Location: ../index.php?error=1');
exit();
?>