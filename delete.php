<?php 
session_start();
include('conexion.php');

if (isset($_GET['id_publicacion'])) {
    $id_publicacion = $_GET['id_publicacion'];
    $sql_eliminar_comentarios = "DELETE FROM comentarios WHERE id_publicacion = $id_publicacion";
    $resultado_eliminar_comentarios = mysqli_query($con, $sql_eliminar_comentarios);
    if (!$resultado_eliminar_comentarios) {
        echo "ERROR: No se pudieron eliminar los comentarios.";
        exit();
    }
    $sql_eliminar_megusta = "DELETE FROM megusta WHERE id_publicacion = $id_publicacion";
    $resultado_eliminar_megusta = mysqli_query($con, $sql_eliminar_megusta);
    if (!$resultado_eliminar_megusta) {
        echo "ERROR: No se pudieron eliminar los 'Me gusta'.";
        exit();
    }
    $sql_eliminar_publicacion = "DELETE FROM publicaciones WHERE id_publicacion = $id_publicacion";
    $resultado_eliminar_publicacion = mysqli_query($con, $sql_eliminar_publicacion);
    if (!$resultado_eliminar_publicacion) {
        echo "ERROR: No se pudo eliminar la publicación.";
    } else {
        header("Location: perfil.php");
        exit();
    }
} else {
    echo "ERROR: Falta el ID de la publicación.";
}

?>
