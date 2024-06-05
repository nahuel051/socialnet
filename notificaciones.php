<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'];
}

$sql_notificaciones = "SELECT n.*, u.username, p.imagen, c.comentario 
                      FROM notificaciones n
                      LEFT JOIN usuarios u ON n.object_id = u.id_usuario
                      LEFT JOIN publicaciones p ON n.object_id = p.id_publicacion
                      LEFT JOIN comentarios c ON n.object_id = c.id_comentario
                      WHERE n.id_usuario = '$id_usuario'
                      ORDER BY n.fecha_notificacion DESC";

$result_notificaciones = mysqli_query($con, $sql_notificaciones);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Notificaciones</h1>
    <div class="notificaciones">
        <?php
        if (mysqli_num_rows($result_notificaciones) > 0) {
            while ($row_notificacion = mysqli_fetch_assoc($result_notificaciones)) {
                $id_notificacion = $row_notificacion['id_notificacion'];
                $type = $row_notificacion['type'];
                $username = $row_notificacion['username'];
                $id_publicacion = $row_notificacion['object_id'];
                $comentario = $row_notificacion['comentario'];
                $fecha = $row_notificacion['fecha_notificacion'];

                echo "<div class='notificacion'>";
                switch ($type) {
                    case 'like':
                        echo "<p>$username le dio Me gusta a tu <a href='ver_publicacion.php?id_publicacion=$id_publicacion'>publicación</a></p>";
                        break;
                    case 'follow':
                        echo "<p>$username te ha seguido</p>";
                        break;
                    case 'comment':
                        echo "<p>$username comentó en tu <a href='ver_publicacion.php?id_publicacion=$id_publicacion'>publicación</a> $comentario</p>";
                        break;
                }
                echo "<p><small>$fecha</small></p>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay notificaciones.</p>";
        }
        ?>
    </div>
</body>
</html>
