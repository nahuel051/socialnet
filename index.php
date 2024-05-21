<?php
include('conexion.php');
session_start();
if(!isset($_SESSION['registrar'])){ // Aquí debería ser 'registro', no 'usuario'
    header('Location: login.html');
    exit(); // Agrega esto para asegurarte de que se detenga la ejecución del script después de redirigir
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
    <a href="cerrar.php">Cerrar Sesion</a>
    <a href="publicar.html">Publicar</a>
</body>
</html>