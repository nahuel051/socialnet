<?php
session_start();
include('conexion.php');
if(!isset($_SESSION['registrar'])){ 
    header('Location: login.html');
    exit(); 
}
$mensaje = "";
if($_POST){
    $imagen = $_FILES['imagen'];
    $descripcion = $_POST['descripcion'];
    if(empty($imagen)){
        $mensaje = "Agregar imagen o video!";
    }else{
         $nombre_img = $imagen['name'];
         $temp_img = $imagen['tmp_name'];
         $extension = strtolower(pathinfo($nombre_img, PATHINFO_EXTENSION));
 
         if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png' || $extension === 'mp4') {
             move_uploaded_file($temp_img, 'publicaciones/' . $nombre_img);
 
             $ruta_img = 'publicaciones/' . $nombre_img; 

             $id_usuario = $_SESSION['registrar']['id_usuario'];
         }else{
            $mensaje = "Error de archivo!";
         }

         if(empty($mensaje)){
            $sql = "INSERT INTO publicaciones (id_usuario, imagen, descripcion) VALUES ('$id_usuario', '$ruta_img', '$descripcion')";
            $guardar = mysqli_query($con,$sql);
            if($guardar){
                echo "success";
            }else{
                $mensaje = "ERROR!" .mysqli_error($con);
            }
        }
    }
        if($mensaje != ""){
        echo $mensaje;
    }
}
?>
