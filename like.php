<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) { 
    header('Location: login.html');
    exit(); 
}
$id_usuario = $_SESSION['registrar']; // Asegúrate de que esto es un ID y no un array
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario']; // Ajusta esto según tu estructura de sesión
}

if (isset($_POST['like'])) {
    $id_publicacion = $_POST['id_publicacion']; // Obtener el id_publicacion desde el formulario

    if (!empty($id_publicacion) && !empty($id_usuario)) {
        $sql = "INSERT INTO megusta (id_publicacion, id_usuario) VALUES ('$id_publicacion', '$id_usuario')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            header('Location: index.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Error: Falta id_publicacion o id_usuario";
    }
}
?>
