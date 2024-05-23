<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario']; // Ajusta esto según tu estructura de sesión
}

if ($_POST) {
    $id_comentario = $_POST['id_comentario'];

    // Verificar que el comentario pertenece al usuario actual
    $sql_verificar = "SELECT id_usuario FROM comentarios WHERE id_comentario = '$id_comentario'";
    $result_verificar = mysqli_query($con, $sql_verificar);
    $row_verificar = mysqli_fetch_assoc($result_verificar);

    if ($row_verificar['id_usuario'] == $id_usuario) {
        $sql = "DELETE FROM comentarios WHERE id_comentario = '$id_comentario'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No tienes permiso para eliminar este comentario']);
    }
}
?>
