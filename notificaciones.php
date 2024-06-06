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
        if (mysqli_num_rows($result) > 0):
            while ($row = mysqli_fetch_assoc($result)): ?>
                <li>
                    <?php if ($row['type'] == 'like'): ?>
                        <a href="perfil.php?id_publicacion=<?php echo $row['object_id']; ?>">
                            <?php echo $row['actor_username']; ?> le dio me gusta a tu publicación "<?php echo $row['publicacion_descripcion']; ?>"
                        </a>
                    <?php elseif ($row['type'] == 'comment'): ?>
                        <a href="perfil.php?id_publicacion=<?php echo $row['object_id']; ?>">
                            <?php echo $row['actor_username']; ?> comentó en tu publicación "<?php echo $row['publicacion_descripcion']; ?>"
                        </a>
                    <?php elseif ($row['type'] == 'follow'): ?>
                        <a href="perfil.php?id_usuario=<?php echo $row['actor_id']; ?>">
                            <?php echo $row['actor_username']; ?> te empezó a seguir
                        </a>
                    <?php endif; ?>
                </li>
            <?php 
            endwhile;
        else:
            echo "<p>No tienes notificaciones.</p>";
        endif;
        ?>
    </ul>
</body>
</html>
