<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    exit('No tiene acceso');
}
$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'];
}

if (isset($_GET['query'])) {
    $terminoBusqueda = mysqli_real_escape_string($con, $_GET['query']);

    $sql_busqueda = "
        SELECT u.id_usuario, u.username, u.foto_perfil 
        FROM usuarios u 
        WHERE u.username LIKE '%$terminoBusqueda%'
          AND u.id_usuario != '$id_usuario'
    ";
    $result_busqueda = mysqli_query($con, $sql_busqueda);

    while ($row_busqueda = mysqli_fetch_array($result_busqueda)) {
        $is_following = false;
        $sql_check_follow = "SELECT * FROM seguidores WHERE id_seguidor = '$id_usuario' AND id_siguiendo = '$row_busqueda[id_usuario]'";
        $result_check_follow = mysqli_query($con, $sql_check_follow);
        if (mysqli_num_rows($result_check_follow) > 0) {
            $is_following = true;
        }
        ?>
        <div class="usuario">
            <img width="50" height="50" src="<?php echo $row_busqueda['foto_perfil']; ?>" alt="Foto de perfil">
            <a href="otro_perfil.php?id_usuario=<?php echo $row_busqueda['id_usuario']?>"><?php echo $row_busqueda['username']; ?></a>
            <button class="follow-btn" data-id-siguiendo="<?php echo $row_busqueda['id_usuario']; ?>">
                <?php echo $is_following ? 'Dejar de seguir' : 'Seguir'; ?>
            </button>
        </div>
        <?php
    }
}
?>
