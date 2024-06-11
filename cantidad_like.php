<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

//Verifica si el parametro id_publicacion esta presente en la url como parte de la consulta GET.
if (isset($_GET['id_publicacion'])) {
//si esta presente se asigna su valor a la variable $id_publicacion
    $id_publicacion = $_GET['id_publicacion'];
} else {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios que dieron Like</title>
</head>
<body>
<?php include('lateral.php') ?>

<div class="likes">
    <h3>Usuarios que dieron Like</h3>
    <?php
//megusta es tabla principal
//WHERE m.id_publicacion = $id_publicacion: 
// Esta cláusula WHERE filtra los resultados para que solo se devuelvan 
// las filas donde el id_publicacion en la tabla megusta coincida 
// con el valor específico de $id_publicacion proporcionado en la consulta.
    $sql_likes = "
        SELECT u.username, u.foto_perfil 
        FROM megusta m
        JOIN usuarios u ON m.id_usuario = u.id_usuario
        WHERE m.id_publicacion = $id_publicacion
    ";
    $result_likes = mysqli_query($con, $sql_likes);
//Este condicional verifica si la $result_likes no es nula
//y si el numero de filas es mayor a 0 y asegura que haya resultados
    if ($result_likes && mysqli_num_rows($result_likes) > 0) {
        while ($row_like = mysqli_fetch_assoc($result_likes)) {
            echo "<div class='usuario'>
                    <img width='50' height='50' src='" . $row_like['foto_perfil'] . "' alt='Foto de perfil'>
                    <span>" . $row_like['username'] . "</span>
                  </div>";
        }
    } else {
        echo "<p>No hay usuarios que hayan dado Like a esta publicación.</p>";
    }
    ?>
</div>

</body>
</html>
