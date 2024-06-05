<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_publicacion = $_GET['id_publicacion'];

$sql_publicacion = "SELECT p.*, u.username 
                   FROM publicaciones p
                   LEFT JOIN usuarios u ON p.id_usuario = u.id_usuario
                   WHERE p.id_publicacion = '$id_publicacion'";

$result_publicacion = mysqli_query($con, $sql_publicacion);
$publicacion = mysqli_fetch_assoc($result_publicacion);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Publicación</h1>
    <?php if ($publicacion): ?>
        <div class="publicacion">
            <p><strong><?php echo $publicacion['username']; ?></strong></p>
            <p><?php echo $publicacion['descripcion']; ?></p>
            <?php if ($publicacion['imagen']): ?>
                <img src="<?php echo $publicacion['imagen']; ?>" alt="Publicación">
            <?php endif; ?>
            <p><small><?php echo $publicacion['fecha_publicacion']; ?></small></p>
        </div>
    <?php else: ?>
        <p>Publicación no encontrada.</p>
    <?php endif; ?>
</body>
</html>
