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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar Usuarios</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('lateral.php') ?>

    <div class="explorar-usuarios">
        <h3>Explorar Usuarios</h3>
        <?php
        // Seleccionar usuarios que no están siendo seguidos por el usuario actual
        $sql_explorar = "
            SELECT u.id_usuario, u.username, u.foto_perfil 
            FROM usuarios u 
            WHERE u.id_usuario != '$id_usuario' 
              AND u.id_usuario NOT IN (SELECT id_siguiendo FROM seguidores WHERE id_seguidor = '$id_usuario')
        ";
        $result_explorar = mysqli_query($con, $sql_explorar);
        while ($row_explorar = mysqli_fetch_array($result_explorar)) {
            $is_following = false;
            $sql_check_follow = "SELECT * FROM seguidores WHERE id_seguidor = '$id_usuario' AND id_siguiendo = '$row_explorar[id_usuario]'";
            $result_check_follow = mysqli_query($con, $sql_check_follow);
            if (mysqli_num_rows($result_check_follow) > 0) {
                $is_following = true;
            }
        ?>
        <div class="usuario">
            <img width="50" height="50" src="<?php echo $row_explorar['foto_perfil']; ?>" alt="Foto de perfil">
            <strong><?php echo $row_explorar['username']; ?></strong>
            <button class="follow-btn" data-id-siguiendo="<?php echo $row_explorar['id_usuario']; ?>">
                <?php echo $is_following ? 'Dejar de seguir' : 'Seguir'; ?>
            </button>
        </div>
        <?php
        }
        ?>
    </div>

    <script>
        $(document).ready(function() {
            // Manejar seguimiento de usuarios
            $('.follow-btn').on('click', function() {
                var button = $(this);
                var id_siguiendo = button.data('id-siguiendo');
                var action = button.text().trim() === 'Seguir' ? 'seguir' : 'dejar_de_seguir';

                $.ajax({
                    type: 'POST',
                    url: action + '.php',
                    data: { id_siguiendo: id_siguiendo },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            if (action === 'seguir') {
                                button.text('Dejar de seguir');
                            } else {
                                button.text('Seguir');
                            }
                        } else {
                            alert(data.error);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
