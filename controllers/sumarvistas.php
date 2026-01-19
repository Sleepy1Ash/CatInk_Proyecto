<?php
    include("../data/conexion.php");

    if (isset($_POST['noticia_id'])) {
        $noticia_id = intval($_POST['noticia_id']);
        
        // Usar sentencias preparadas para seguridad
        $sql = "UPDATE noticias SET vistas = vistas + 1 WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $noticia_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Vista sumada"]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => $con->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Falta el ID"]);
    }
?>    