<?php
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Si se desea eliminar la sesión, también se debe eliminar la cookie de sesión.
// Nota: ¡Esto destruirá la sesión y no la información de la sesión!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión
session_destroy();

// Redirige al usuario a la página de inicio de sesión o a la página principal de tu sitio web
header('Location: ../index.php');
exit;
?>