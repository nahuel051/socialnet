<?php
include('conexion.php');
session_start();
$mensaje_like = "";
if (!isset($_SESSION['registrar'])) { 
    header('Location: login.html');
    exit(); 
}

$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'];
}

if (isset($_POST['like'])) {
    $id_publicacion = $_POST['id_publicacion'];

    if (!empty($id_publicacion) && !empty($id_usuario)) {
        // Verificar si ya existe un "Me gusta" de este usuario para esta publicación
        $check_like_sql = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
        $check_like_result = mysqli_query($con, $check_like_sql);
        
        if (mysqli_num_rows($check_like_result) == 0) {
            // No existe un "Me gusta", insertar uno nuevo
            $sql = "INSERT INTO megusta (id_publicacion, id_usuario) VALUES ('$id_publicacion', '$id_usuario')";
            $result = mysqli_query($con, $sql);
            if ($result) {
                header('Location: index.php');
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            $mensaje_like = "Ya has dado 'Me gusta' a esta publicación.";
        }
    }
}
?>
