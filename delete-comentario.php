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
//Se recupera el ID del comentario enviado en la solicitud POST y se almacena en la variable $id_comentario.
    $id_comentario = $_POST['id_comentario'];

    // Verificar que el comentario pertenece al usuario actual
    $sql_verificar = "SELECT id_usuario FROM comentarios WHERE id_comentario = '$id_comentario'";
    $result_verificar = mysqli_query($con, $sql_verificar);
    if ($result_verificar) {
        $row_verificar = mysqli_fetch_assoc($result_verificar);
    // Se compara el ID del usuario asociado al comentario con el ID del usuario actual ($id_usuario).
    // Si son iguales, significa que el usuario actual es el propietario del comentario y tiene permiso para eliminarlo.
        if ($row_verificar['id_usuario'] == $id_usuario) {
            $sql_eliminar = "DELETE FROM comentarios WHERE id_comentario = '$id_comentario'";
            if (mysqli_query($con, $sql_eliminar)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar el comentario.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No tiene permiso para eliminar este comentario.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Comentario no encontrado.']);
    }
}
?>
