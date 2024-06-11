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

//Devuelve true si like_action esta presente en la solicitud POST
//lo que indica que se envio una accion de megusta o quitarmegusta
if (isset($_POST['like_action'])) {
    $id_publicacion = $_POST['id_publicacion'];
//Contiene la acción a realizar:
//'add' para agregar un "me gusta" o 'remove' para eliminarlo.
    $like_action = $_POST['like_action'];

    if (!empty($id_publicacion) && !empty($id_usuario)) {
        if ($like_action === 'add') {
            // Agregar Me gusta
            //Consulta SQL para verificar si ya existe un "me gusta" 
            //de este usuario para esta publicación.
            $check_like_sql = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
            $check_like_result = mysqli_query($con, $check_like_sql);
            //Se verifica si el resultado de la consulta no contiene filas 
            if (mysqli_num_rows($check_like_result) == 0) {
            //Consulta SQL para insertar un nuevo "me gusta" en la tabla megusta
                $sql = "INSERT INTO megusta (id_publicacion, id_usuario) VALUES ('$id_publicacion', '$id_usuario')";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    // Insertar notificación
                    $sql_notificacion = "INSERT INTO notificaciones (id_usuario, actor_id, type, object_id) VALUES (
                        (SELECT id_usuario FROM publicaciones WHERE id_publicacion = '$id_publicacion'), 
                        '$id_usuario', 
                        'like', 
                        '$id_publicacion')";
                    mysqli_query($con, $sql_notificacion);
                    //Si la inserción de la notificación es exitosa, se envía una respuesta JSON con success: true.
                    //exit(); detiene la ejecución del script
                    echo json_encode(array('success' => true));
                    exit();
                }
            }        
        } elseif ($like_action === 'remove') {
            // Eliminar Me gusta
            $sql = "DELETE FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
            $result = mysqli_query($con, $sql);
            if ($result) {
                echo json_encode(array('success' => true));
                exit();
            }
        }
    }
}
?>
