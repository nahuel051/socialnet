<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
$id_usuario_sesion = $_SESSION['registrar'];
if (is_array($id_usuario_sesion)) {
    $id_usuario_sesion = $id_usuario_sesion['id_usuario'];
}

if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];
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
    <title>Seguidores</title>
</head>
<body>
<?php include('lateral.php') ?>
<div class="seguidores">
    <h3>Seguidores</h3>
    <?php
        //Selecciona los datos de la tabla usuarios par los usuarios seguidos por el usuario "visitado"
    //se filta para obtener solo registro donde id_siguiendo es igual a $id_usuario osea el otro perfil
    $sql_seguidores = "SELECT u.id_usuario, u.username, u.foto_perfil 
                       FROM seguidores s 
                       JOIN usuarios u ON s.id_seguidor = u.id_usuario 
                       WHERE s.id_siguiendo = $id_usuario";
    $result_seguidores = mysqli_query($con, $sql_seguidores);
    while ($row_seguidor = mysqli_fetch_array($result_seguidores)) {
    //$link determina el enlace del perfil de usuario
    //si es igual a id_usuario_sesion el enlace es perfil.php
    //de lo contrario otro_perfil.php
        $link = $row_seguidor['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_seguidor['id_usuario'];
        echo "<div><img src='" . $row_seguidor['foto_perfil'] . "' width='50' height='50'> " .
        "<a href='" . $link . "'>" . $row_seguidor['username'] . "</a></div>";
    }
    ?>
</div>
</body>
</html>

