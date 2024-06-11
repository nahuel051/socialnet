<?php
// Este script maneja la lógica para agregar un comentario a una publicación
// y devolver una respuesta JSON que indica el éxito o el error de la operación,
// además de los detalles del comentario.
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
// Inicializar el array de respuesta
$response = array(); 

if ($_POST) {
    $comentario = trim($_POST['comentario']);
    $id_publicacion = $_POST['id_publicacion'];
    $fecha_comentario = date('Y-m-d H:i:s');

    if (!empty($comentario) && !empty($id_publicacion) && !empty($id_usuario_sesion)) {
        $sql = "INSERT INTO comentarios (comentario, id_publicacion, id_usuario, fecha_comentario) VALUES ('$comentario', '$id_publicacion', '$id_usuario_sesion', '$fecha_comentario')";
        $result = mysqli_query($con, $sql);

        //si es ecitosa se procede...
        if ($result) {
        //se obtene el id del comentario recien insertado
            $id_comentario = mysqli_insert_id($con);

            // Obtener el nombre de usuario que esta inciado sesion
            $sql_usuario = "SELECT username FROM usuarios WHERE id_usuario = '$id_usuario_sesion'";
            $result_usuario = mysqli_query($con, $sql_usuario);
            //Extrae la información del nombre de usuario del resultado de la consulta.
            $row_usuario = mysqli_fetch_assoc($result_usuario);
            $username = $row_usuario['username'];

            // Obtener la URL del perfil del usuario
            $profile_link = $id_usuario_sesion == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $id_usuario_sesion;

            // Insertar notificación
            //-La subconsulta obtiene el id_usuario del autor de la publicacion especifica
            //busca en la tabla publicaciones el id_usuario de la fila donde id_publicacion es igual a $id_publicacion
            //el id_usuario es el propietario de la publicacion y quien recibe la notificacion
            //-Comment es una cadena de texto que indica el tipo de accion notificada
            $sql_notificacion = "INSERT INTO notificaciones (id_usuario, actor_id, type, object_id) VALUES (
                (SELECT id_usuario FROM publicaciones WHERE id_publicacion = '$id_publicacion'), 
                '$id_usuario_sesion', 
                'comment', 
                '$id_publicacion')";
            mysqli_query($con, $sql_notificacion);

            //Agregar datos al array de respuesta indicando que la operacion fue exitosa
            //y proporciona el detalle del comentario
            //La respuesta esta en formato JSON
            $response['success'] = true;
            $response['id_comentario'] = $id_comentario;
            $response['username'] = $username;
            $response['comentario'] = $comentario;
            $response['profile_link'] = $profile_link;
        } else {
            //Mensaje de error
            $response['success'] = false;
            $response['error'] = mysqli_error($con);
        }
    } else {
        //Mensaje de error
        $response['success'] = false;
        $response['error'] = 'Ingrese comentario';
    }
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
