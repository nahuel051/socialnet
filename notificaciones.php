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

//Selecciona todas las columnas de notificaciones
//quien genera la notifiacion como actor_username
//y para referirse a la publicacion muestra la descripcion de la publicacion
//actor_id es quien hace la notificacion
//object_id es quien recibe
//LEFT JOIN publicaciones p ON n.object_id = p.id_publicacion permite obtener la descripción de la publicación 
//WHERE n.id_usuario = '$id_usuario filtra solo las notifaciones de $id_usuario
$sql = "SELECT n.*, u.username AS actor_username, p.descripcion AS publicacion_descripcion 
        FROM notificaciones n 
        JOIN usuarios u ON n.actor_id = u.id_usuario 
        LEFT JOIN publicaciones p ON n.object_id = p.id_publicacion 
        WHERE n.id_usuario = '$id_usuario' 
        ORDER BY n.fecha_notificacion DESC";
$result = mysqli_query($con, $sql);

if (!$result) {
    die('Error en la consulta: ' . mysqli_error($con));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
</head>
<body>
    <h1>Notificaciones</h1>
    <ul>
        <?php 
        //comprobar si la consulta devuelve algun resultados
        if (mysqli_num_rows($result) > 0):
        //Devuelve una fila de resultados
            while ($row = mysqli_fetch_assoc($result)): ?>
                <li>
            <!-- Si el tipo de notifiacion es like muestra un enlace muestra un
            enlace donde $row['actor_username'] (el nombre del usuario que 
            realizó la acción) le dio me gusta a la publicación descrita 
            en $row['publicacion_descripcion'].
            Lo mismo con comment y follow -->
                    <?php if ($row['type'] == 'like'): ?>
                        <a href="perfil.php">
                            <?php echo $row['actor_username']; ?> le dio me gusta a tu publicación "<?php echo $row['publicacion_descripcion']; ?>"
                        </a>
                    <?php elseif ($row['type'] == 'comment'): ?>
                        <a href="perfil.php">
                            <?php echo $row['actor_username']; ?> comentó en tu publicación "<?php echo $row['publicacion_descripcion']; ?>"
                        </a>
                    <?php elseif ($row['type'] == 'follow'): ?>
                        <a href="cantidad_seguidores.php">
                            <?php echo $row['actor_username']; ?> te empezó a seguir
                        </a>
                    <?php endif; ?>
                </li>
            <?php 
            //finaliza el bucle
            endwhile;
        else:
            echo "<p>No tienes notificaciones.</p>";
        endif;
        ?>
    </ul>
</body>
</html>
