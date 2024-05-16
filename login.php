<?php
session_start();
include('conexion.php');
include('validaciones.php');
$mensaje = $mensaje_login = "";

if(isset($_POST['registrar'])){
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
                $mensajeEmail = "Este correo electrónico ya está registrado.";
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
                header('Location: index.php');
            }else{
                $mensaje = "ERROR!". mysqli_error($con);
            }
        }
    }

}

//Login

if(isset($_POST['ingresar'])){
    $login_username = $_POST['login_username'];
    $login_password = $_POST['login_password'];

    if(empty($login_username) || empty($login_password)){
        $mensaje_login = "Todo los campos son obligatorios";
    }else{
    $sql = "SELECT * FROM usuarios WHERE username = '$login_username'";
    $result = mysqli_query($con, $sql);
    $login = mysqli_fetch_assoc($result);
    if($login){
            if($login['password'] === $login_password){
                $_SESSION['registrar'] = $login;
                header('Location:index.php');
            }else{
            $mensaje_login = "Contraseña Incorrecta.";
            }
    }else{
    $mensaje_login = "Nombre de usuario Incorrecto.";
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
    <form action="login.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="text" name="username" placeholder="Nombre de usuario">
        <input type="email" name="email" placeholder="Email">
        <input type="file" name="foto_perfil">
        <input type="password" name="password" placeholder="Contraseña">
        <input type="password" name="password2" placeholder="Repetir contraseña">
        <input type="submit" value="Registrar" name="registrar">
        <?php echo $mensaje?>
    </form>
    <hr>
    <form action="login.php" method="post" name="login_form">
        <input type="text" name="login_username" placeholder="Username">
        <input type="password" name="login_password" placeholder="Password">
        <input type="submit" value="Ingresar" name="ingresar">
        <?php echo $mensaje_login?>
        
    </form>
</body>
</html>
