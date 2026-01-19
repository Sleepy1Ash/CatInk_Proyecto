<?php
    include("../data/conexion.php");

    if (isset($_POST['noticia_id']) && isset($_POST['tiempo'])) {
        $noticia_id = intval($_POST['noticia_id']);
        $tiempo = intval($_POST['tiempo']);

        // Insertamos un nuevo registro de tiempo para esta visita
        // Asumimos que la tabla tiene las columnas: noticia_id, tiempo_segundos, fecha (default CURRENT_TIMESTAMP)
        $sql = "INSERT INTO noticias_stats (noticia_id, tiempo_segundos, fecha) VALUES (?, ?, NOW())";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ii", $noticia_id, $tiempo);
        
        if ($stmt->execute()) {
             // Éxito (sendBeacon no espera respuesta, pero es buena práctica)
             echo "Tiempo guardado";
        } else {
             // Si falla el insert, intentamos crear la tabla si no existe (opcional, pero ayuda a depurar)
             // O simplemente registramos el error
             error_log("Error al guardar tiempo: " . $con->error);
        }
    }
?>