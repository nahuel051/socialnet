<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_usuario_sesion = $_SESSION['registrar'];
if (is_array($id_usuario_sesion)) {
    $id_usuario_sesion = $id_usuario_sesion['id_usuario'];
}

if ($_POST) {
    $comentario = $_POST['comentario'];
    $id_publicacion = $_POST['id_publicacion'];
    $fecha_comentario = date('Y-m-d H:i:s');

    $sql = "INSERT INTO comentarios (comentario, id_publicacion, id_usuario, fecha_comentario) VALUES ('$comentario', '$id_publicacion', '$id_usuario_sesion', '$fecha_comentario')";

    if (mysqli_query($con, $sql)) {
        $id_comentario = mysqli_insert_id($con);
        $sql_usuario = "SELECT username FROM usuarios WHERE id_usuario = '$id_usuario_sesion'";
        $result_usuario = mysqli_query($con, $sql_usuario);
        $row_usuario = mysqli_fetch_assoc($result_usuario);
        $username = $row_usuario['username'];
        
        echo json_encode(['success' => true, 'id_comentario' => $id_comentario, 'username' => $username, 'comentario' => $comentario]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al guardar el comentario.']);
    }
}
?>
