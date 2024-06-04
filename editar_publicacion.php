<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
$mensaje = "";
if(isset($_GET['id_publicacion'])){
    $id = $_GET['id_publicacion'];
    $sql = "SELECT * FROM publicaciones WHERE id_publicacion = $id";
    $result= mysqli_query($con, $sql);
    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_array($result);
        $ruta_img = $row['imagen'];
        $descripcion = $row['descripcion'];
    }
}
if(isset($_POST['guardar'])){
    $id = $_GET['id_publicacion'];
    $imagen = $_FILES['imagen'];
    $descripcion = $_POST['descripcion'];
    if(empty($imagen) || empty($descripcion)){
        $mensaje = "El nombre y la foto de perfil son obligatorios.";
    } else {
        if(!empty($imagen['name'])){
            $nombre_img = $imagen['name'];
            $temp_img = $imagen['tmp_name'];
            $extension = strtolower(pathinfo($nombre_img, PATHINFO_EXTENSION));
        
            if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
                move_uploaded_file($temp_img, 'imagenes/' . $nombre_img);
                $ruta_img = 'imagenes/' . $nombre_img;
            } else {
                $mensaje = "ERROR! Solo se permiten archivos JPG o PNG.";
            }
        }
        
        if(empty($mensaje)){
        $sql = "UPDATE publicaciones SET descripcion = '$descripcion', imagen = '$ruta_img' WHERE id_publicacion = '$id'";
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
    <form action="editar_publicacion.php?id_publicacion=<?php echo $_GET['id_publicacion']?>" method="post" enctype="multipart/form-data" autocomplete="off">
    <input type="file" name="imagen">
    <img width="150" height="180" src="<?php echo $ruta_img; ?>" alt="imagen">
    <textarea name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>
    <input type="submit" value="Guardar cambios" name="guardar">
    <?php echo $mensaje ?> 
    </form>
</body>
</html>