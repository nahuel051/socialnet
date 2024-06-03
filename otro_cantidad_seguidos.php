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
    // Redireccionar a un lugar adecuado si no se proporciona un id de usuario
    header('Location: index.php');
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
<?php include('lateral.php') ?>
<div class="seguidos">
    <h3>Seguidos</h3>
    <?php
    $sql_seguidos = "SELECT u.id_usuario, u.username, u.foto_perfil FROM seguidores s JOIN usuarios u ON s.id_siguiendo = u.id_usuario WHERE s.id_seguidor = $id_usuario";
    $result_seguidos = mysqli_query($con, $sql_seguidos);
    while ($row_seguido = mysqli_fetch_array($result_seguidos)) {
        echo "<div><img src='" . $row_seguido['foto_perfil'] . "' width='50' height='50'> " . $row_seguido['username'] . "</div>";
    }
    ?>
</div>
</body>
</html>
