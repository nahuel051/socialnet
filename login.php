<?php 
session_start();
include('conexion.php');
include('validaciones.php');

$mensaje = "";

if($_POST){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
        $mensaje = "Todo los campos son obligatorios";
    }else{
        $sql = "SELECT * FROM usuarios WHERE username = '$username'";
        $result = mysqli_query($con, $sql);
        $login = mysqli_fetch_assoc($result);
        if($login){
            if($login['password'] === $password){
                $_SESSION['registrar'] = $login;
                echo "success";
            }else{
                $mensaje = "Contraseña Incorrecta.";
            }
        }else{
            $mensaje = "Nombre de usuario Incorrecto.";
        }
    }

    if($mensaje != ""){
        echo $mensaje;
    }
}
?>
