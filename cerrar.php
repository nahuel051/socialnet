<?php
session_start();
include('conexion.php');
if(!isset($_SESSION['registrar'])){
    header('Location: login.php');
    exit();
}
session_destroy();
header("Location:login.php");
exit();
?>