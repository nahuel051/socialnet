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

if(isset($_GET['id_usuario'])){
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
    <title>Seguidos</title>
</head>
<body>
<?php include('lateral.php') ?>
<div class="seguidos">
    <h3>Seguidos</h3>
    <?php
    //Selecciona los datos de la tabla usuarios par los usuarios seguidos por el usuario "visitado"
    //se filta para obtener solo registro donde id_seguidor es igual a $id_usuario osea el otro perfil
    $sql_seguidos = "SELECT u.id_usuario, u.username, u.foto_perfil FROM seguidores s JOIN usuarios u ON s.id_siguiendo = u.id_usuario WHERE s.id_seguidor = $id_usuario";
    $result_seguidos = mysqli_query($con, $sql_seguidos);
    while ($row_seguido = mysqli_fetch_array($result_seguidos)) {
    //$link determina el enlace del perfil de usuario
    //si es igual a id_usuario_sesion el enlace es perfil.php
    //de lo contrario otro_perfil.php
        $link = $row_seguido['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_seguido['id_usuario'];
        echo "<div><img src='" . $row_seguido['foto_perfil'] . "' width='50' height='50'> " .
        "<a href='" . $link . "'>" . $row_seguido['username'] . "</a></div>";
    }
    ?>
</div>
</body>
</html>
