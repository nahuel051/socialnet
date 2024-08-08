<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

//Obtener el id del seguidor
//la variable almacena el id del usuario que esta realizando la accion
$id_seguidor = $_SESSION['registrar']['id_usuario'];
//el POST obtiene el id del usuario que sea desea seguir
//cuando se hace clic se envia desde una solicitud POST
//la variable almacena este ID que representa al usuario que sea seguido por id_seguidor
$id_siguiendo = $_POST['id_siguiendo'];

// Verificación de Auto-seguimiento:
// if ($id_seguidor == $id_siguiendo) {
//     echo json_encode(['success' => false, 'error' => 'No puedes seguirte a ti mismo']);
//     exit();
// }

$sql = "INSERT INTO seguidores (id_seguidor, id_siguiendo) VALUES ('$id_seguidor', '$id_siguiendo')";
$result = mysqli_query($con, $sql);

if ($result) {
    // Insertar notificación
    $sql_notificacion = "INSERT INTO notificaciones (id_usuario, type, object_id, actor_id) VALUES ('$id_siguiendo', 'follow', '$id_seguidor', '$id_seguidor')";
    mysqli_query($con, $sql_notificacion);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al seguir al usuario']);
}
?>
