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

if ($_POST) {
    $comentario = trim($_POST['comentario']);
    $id_publicacion = $_POST['id_publicacion'];

    if (!empty($comentario) && !empty($id_publicacion) && !empty($id_usuario)) {
        $sql = "INSERT INTO comentarios (id_publicacion, id_usuario, comentario) VALUES ('$id_publicacion', '$id_usuario', '$comentario')";
        $result = mysqli_query($con, $sql);
        if ($result) {
            header('Location: index.php');
            exit();
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Ingrese comentario";
    }
}
?>
