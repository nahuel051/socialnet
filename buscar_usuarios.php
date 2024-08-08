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

//Comprueba si query fue enviado a traves de una solicitud GET
if (isset($_GET['query'])) {
// La función mysqli_real_escape_string asegura que cualquier 
// carácter especial en la cadena proporcionada se escape 
// correctamente, haciendo la cadena segura para usar en una 
// consulta SQL.
// se utiliza $terminoBusqueda en una consulta SQL para buscar 
// usuarios cuyo nombre de usuario contenga el término 
// de búsqueda.
$terminoBusqueda = mysqli_real_escape_string($con, $_GET['query']);
    // Obtener el término de búsqueda directamente
    // $terminoBusqueda = $_GET['query'];

// Buscar usuarios cuyo nombre de usuario contenga el término de búsqueda y que no sean el usuario actual
    $sql_busqueda = "
        SELECT u.id_usuario, u.username, u.foto_perfil 
        FROM usuarios u 
        WHERE u.username LIKE '%$terminoBusqueda%'
          AND u.id_usuario != '$id_usuario'
    ";
    $result_busqueda = mysqli_query($con, $sql_busqueda);

    // Mostrar resultados de búsqueda
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
