<?php include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
$mensaje = "";
if(isset($_GET['id_usuario'])){
    $id = $_GET['id_usuario'];
    $sql = "SELECT * FROM usuarios WHERE `id_usuario` = $id";
    $result= mysqli_query($con, $sql);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_array($result);
        $username = $row['username'];
        $ruta_img = $row['foto_perfil'];
    }
}
if(isset($_POST['guardar'])){
    $id = $_GET['id_usuario'];
    $username = $_POST['username'];
    $foto_perfil = $_FILES['foto_perfil'];

    if(empty($username) || empty($foto_perfil)){
        $mensaje = "El nombre y la foto de perfil son obligatorios.";
    } else {
        if(!empty($foto_perfil['name'])){
            $nombre_img = $foto_perfil['name'];
            $temp_img = $foto_perfil['tmp_name'];
            $extension = strtolower(pathinfo($nombre_img, PATHINFO_EXTENSION));
        
            if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
                // Mover el archivo solo si es una imagen permitida
                move_uploaded_file($temp_img, 'imagenes/' . $nombre_img);
                $ruta_img = 'imagenes/' . $nombre_img;
            } else {
                $mensaje = "ERROR! Solo se permiten archivos JPG o PNG.";
            }
        }

        // Verificar duplicados en la base de datos
        $sql = "SELECT * FROM usuarios WHERE username = '$username' AND id_usuario <> $id";
        $result = mysqli_query($con, $sql);
        if (mysqli_num_rows($result) > 0) {
            $mensaje= "El username ya esta registrado";
        }
        
        if(empty($mensaje)){
        // Actualizar los datos del usuario en la base de datos
        $sql = "UPDATE usuarios SET username = '$username', foto_perfil = '$ruta_img' WHERE id_usuario = '$id'";
        $guardar = mysqli_query($con, $sql);
        

        if($guardar){
            header("Location: perfil.php");
            exit();
        } else {
            $mensaje = "Error al guardar en la base de datos.";
        }
        }

    }
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
    <form action="editar_perfil.php?id_usuario=<?php echo $_GET['id_usuario']?>" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="text" name="username" value="<?php echo $username?>">
    <input type="file" name="foto_perfil"> 
    <img width="150" height="180" src="<?php echo $ruta_img; ?>" alt="Foto de perfil">
    <input type="submit" value="Guardar Cambios" name="guardar">
    <?php echo $mensaje ?> 
</form>
</body>
</html>