<?php
session_start();
include('conexion.php');
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}


$id_seguidor = $_SESSION['registrar']['id_usuario'];
$id_siguiendo = $_POST['id_siguiendo'];

if ($id_seguidor == $id_siguiendo) {
    echo json_encode(['success' => false, 'error' => 'No puedes seguirte a ti mismo']);
    exit();
}

$sql = "INSERT INTO seguidores (id_seguidor, id_siguiendo) VALUES ('$id_seguidor', '$id_siguiendo')";
$result = mysqli_query($con, $sql);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al seguir al usuario']);
}
?>
