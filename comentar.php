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

$response = array(); // Inicializar el array de respuesta

if ($_POST) {
    $comentario = trim($_POST['comentario']);
    $id_publicacion = $_POST['id_publicacion'];
    $fecha_comentario = date('Y-m-d H:i:s');

    if (!empty($comentario) && !empty($id_publicacion) && !empty($id_usuario_sesion)) {
        $sql = "INSERT INTO comentarios (comentario, id_publicacion, id_usuario, fecha_comentario) VALUES ('$comentario', '$id_publicacion', '$id_usuario_sesion', '$fecha_comentario')";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $id_comentario = mysqli_insert_id($con);

            // Obtener el nombre de usuario
            $sql_usuario = "SELECT username FROM usuarios WHERE id_usuario = '$id_usuario_sesion'";
            $result_usuario = mysqli_query($con, $sql_usuario);
            $row_usuario = mysqli_fetch_assoc($result_usuario);
            $username = $row_usuario['username'];

            // Obtener la URL del perfil del usuario
            $profile_link = $id_usuario_sesion == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $id_usuario_sesion;

            // Insertar notificación
            $sql_notificacion = "INSERT INTO notificaciones (id_usuario, actor_id, type, object_id) VALUES (
                (SELECT id_usuario FROM publicaciones WHERE id_publicacion = '$id_publicacion'), 
                '$id_usuario_sesion', 
                'comment', 
                '$id_publicacion')";
            mysqli_query($con, $sql_notificacion);

            $response['success'] = true;
            $response['id_comentario'] = $id_comentario;
            $response['username'] = $username;
            $response['comentario'] = $comentario;
            $response['profile_link'] = $profile_link;
        } else {
            $response['success'] = false;
            $response['error'] = mysqli_error($con);
        }
    } else {
        $response['success'] = false;
        $response['error'] = 'Ingrese comentario';
    }
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
