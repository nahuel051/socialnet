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

if (isset($_POST['like_action'])) {
    $id_publicacion = $_POST['id_publicacion'];
    $like_action = $_POST['like_action'];

    if (!empty($id_publicacion) && !empty($id_usuario)) {
        if ($like_action === 'add') {
            // Agregar Me gusta
            $check_like_sql = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
            $check_like_result = mysqli_query($con, $check_like_sql);

            if (mysqli_num_rows($check_like_result) == 0) {
                $sql = "INSERT INTO megusta (id_publicacion, id_usuario) VALUES ('$id_publicacion', '$id_usuario')";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    echo json_encode(array('success' => true));
                    exit();
                } else {
                    echo json_encode(array('success' => false, 'error' => 'Error al agregar Me gusta.'));
                    exit();
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Ya has dado "Me gusta" a esta publicación.'));
                exit();
            }
        } elseif ($like_action === 'remove') {
            // Eliminar Me gusta
            $sql = "DELETE FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
            $result = mysqli_query($con, $sql);
            if ($result) {
                echo json_encode(array('success' => true));
                exit();
            } else {
                echo json_encode(array('success' => false, 'error' => 'Error al eliminar Me gusta.'));
                exit();
            }
        }
    }
}
?>
