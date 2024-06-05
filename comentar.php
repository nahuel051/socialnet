<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'];
}

if ($_POST) {
    $comentario = trim($_POST['comentario']);
    $id_publicacion = $_POST['id_publicacion'];

    if (!empty($comentario) && !empty($id_publicacion) && !empty($id_usuario)) {
        $sql = "INSERT INTO comentarios (id_publicacion, id_usuario, comentario) VALUES ('$id_publicacion', '$id_usuario', '$comentario')";
        $result = mysqli_query($con, $sql);

        if ($result) {
            // Obtener el nombre de usuario
            $sql_usuario = "SELECT username FROM usuarios WHERE id_usuario = '$id_usuario'";
            $result_usuario = mysqli_query($con, $sql_usuario);
            $row_usuario = mysqli_fetch_assoc($result_usuario);
            $username = $row_usuario['username'];

            // Insertar notificación
            $sql_notificacion = "INSERT INTO notificaciones (id_usuario, type, object_id) VALUES ((SELECT id_usuario FROM publicaciones WHERE id_publicacion = '$id_publicacion'), 'comment', '$id_publicacion')";
            mysqli_query($con, $sql_notificacion);

            // Devolver respuesta en formato JSON
            echo json_encode(['success' => true, 'username' => $username, 'comentario' => $comentario]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Ingrese comentario']);
    }
}
?>
