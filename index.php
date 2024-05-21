<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="cerrar.php">Cerrar Sesion</a>
    <a href="publicar.html">Publicar</a>
    <?php 
    $sql_publicaciones = "SELECT * FROM publicaciones";
    $result_publicaciones = mysqli_query($con, $sql_publicaciones);
    while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
    ?>
    <div class="content-post">
        <img width="150" height="180" src="<?php echo $row_publicacion['imagen']; ?>" alt="Publicacion">
        <?php echo $row_publicacion['descripcion'] ?>
        <div class="comentario">
            <form action="comentar.php" method="post">
                <textarea name="comentario" placeholder="Comentar"></textarea>
                <input type="hidden" name="id_publicacion" value="<?php echo $row_publicacion['id_publicacion']; ?>">
                <input type="submit" value="Enviar" name="Comentario">
            </form>
        </div>
        <div class="content-comentario">
        <?php 
            $id_publicacion = $row_publicacion['id_publicacion'];
            $sql_comentarios = "SELECT c.comentario, u.username FROM comentarios c JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.id_publicacion = $id_publicacion ORDER BY c.fecha_comentario ASC";
            $result_comentarios = mysqli_query($con, $sql_comentarios);
            while ($row_comentario = mysqli_fetch_array($result_comentarios)) {
                echo "<p><strong>" . $row_comentario['username'] . ":</strong> " . $row_comentario['comentario'] . "</p>";
            }
            ?>
        </div>
        <input type="submit" value="Me gusta" name="Like">
    </div>
    <?php
    }
    ?>
</body>
</html>
