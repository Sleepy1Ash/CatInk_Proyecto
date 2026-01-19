<?php
include("../data/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Primero eliminamos las estadísticas asociadas para mantener integridad (si no hay ON DELETE CASCADE)
    $stmtStats = $con->prepare("DELETE FROM noticias_stats WHERE noticia_id = ?");
    $stmtStats->bind_param("i", $id);
    $stmtStats->execute();
    $stmtStats->close();

    // Luego eliminamos la noticia
    $stmt = $con->prepare("DELETE FROM noticias WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirigir de vuelta con mensaje de éxito
        header("Location: ../views/contenidos.php?msg=eliminado");
    } else {
        // Redirigir con error
        header("Location: ../views/contenidos.php?error=no_eliminado");
    }
    $stmt->close();
} else {
    header("Location: ../views/contenidos.php");
}
?>