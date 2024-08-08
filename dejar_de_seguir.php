<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_seguidor = $_SESSION['registrar']['id_usuario'];
$id_siguiendo = $_POST['id_siguiendo'];

//DEJAR DE SEGUIR
//Se busca en la tabla seguidores una fila donde se id_seguidor sea igual a $id_seguidor
//y id_siguiendo sea igual a $id_siguiendo y procede a eliminar de la base de datos
$sql = "DELETE FROM seguidores WHERE id_seguidor = '$id_seguidor' AND id_siguiendo = '$id_siguiendo'";
$result = mysqli_query($con, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al dejar de seguir al usuario']);
}
?>
