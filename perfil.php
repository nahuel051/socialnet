<?php include('conexion.php');
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
    <title>Document</title>
</head>
<body>
    <?php include('lateral.php')?>
    <?php $sql = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
          $result = mysqli_query($con, $sql);
          while($row = mysqli_fetch_array($result)){
    ?>
        <div class="content-perfil">
            <?php echo $row['username']?>
            <img width="150" height="180" src="<?php echo $row['foto_perfil']; ?>" alt="Foto de perfil">
            <a href="editar_perfil.php?id_usuario=<?php echo $row['id_usuario']?>">Editar</a>
        </div>
    <?php       
          }
    ?>
</body>
</html>