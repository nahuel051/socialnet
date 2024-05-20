<?php
session_start();
include('conexion.php');
include('validaciones.php');
$mensaje = "";

if($_POST){
    $username = $_POST['username'];
    $email = $_POST['email'];
    $foto_perfil = $_FILES['foto_perfil'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if(empty($username) || empty($email) || empty($foto_perfil) || empty($password)|| empty($password2)){
        $mensaje = "Todo los campos son obligatorios";
    }else{
        if($password !== $password2){
            $mensaje = "Las contraseña no coinciden.";
        }else if(!validarContraseña($password)){
            $mensaje = "<ul> 
            <li>Minimo 8 caracteres</li>
            <li>Minimo 1 mayuscula</li>
            <li>Minimo 1 numero</li>
            </ul>";
        }
            // Verificar duplicados en la base de datos
            $sql = "SELECT * FROM usuarios WHERE username = '$username'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) > 0) {
                $mensaje = "Username ya esta registrado";
            }
    
            $sql = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) > 0) {
                $mensaje = "Este correo electrónico ya está registrado.";
            }

        // Procesar la imagen
        $nombre_img = $foto_perfil['name'];
        $temp_img = $foto_perfil['tmp_name'];
        $extension = strtolower(pathinfo($nombre_img, PATHINFO_EXTENSION));

        // Verificar si es una imagen permitida
        if ($extension === 'jpg' || $extension === 'jpeg' || $extension === 'png') {
            // Mover el archivo solo si es una imagen permitida
            move_uploaded_file($temp_img, 'imagenes/' . $nombre_img);

            // Guardar la ruta de la imagen en la base de datos
            $ruta_img = 'imagenes/' . $nombre_img; 
        }else{
            $mensaje = "ERROR! Solo se permiten archivos JPG o PNG.";
        }
        if(empty($mensaje)){
            $sql = "INSERT INTO usuarios (username, email,  foto_perfil, password)  VALUES ('$username', '$email', '$ruta_img', '$password')"; 
            $guardar = mysqli_query($con, $sql);
            if($guardar){
                echo "success";
            }else{
                $mensaje = "ERROR!". mysqli_error($con);
            }
        }
    }
    if($mensaje != ""){
        echo $mensaje;
    }
}

?>
