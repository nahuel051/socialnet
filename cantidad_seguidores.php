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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguidores</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include('lateral.php') ?>
<div class="seguidores">
    <h3>Seguidores</h3>
    <?php
    $sql_seguidores = "SELECT u.id_usuario, u.username, u.foto_perfil 
                       FROM seguidores s 
                       JOIN usuarios u ON s.id_seguidor = u.id_usuario 
                       WHERE s.id_siguiendo = $id_usuario";
    $result_seguidores = mysqli_query($con, $sql_seguidores);
    while ($row_seguidor = mysqli_fetch_array($result_seguidores)) {
    ?>
        <div class="usuario">
            <img width="50" height="50" src="<?php echo $row_seguidor['foto_perfil']; ?>" alt="Foto de perfil">
            <a href="otro_perfil.php?id_usuario=<?php echo $row_seguidor['id_usuario']; ?>"><?php echo $row_seguidor['username']; ?></a>
        </div>
    <?php
    }
    ?>
</div>
</body>
</html>
