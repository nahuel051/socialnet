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
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php include('lateral.php') ?>
<div class="seguidos">
    <h3>Seguidos</h3>
    <?php
    $sql_seguidos = "SELECT u.id_usuario, u.username, u.foto_perfil FROM seguidores s JOIN usuarios u ON s.id_siguiendo = u.id_usuario WHERE s.id_seguidor = $id_usuario";
    $result_seguidos = mysqli_query($con, $sql_seguidos);
    while ($row_seguido = mysqli_fetch_array($result_seguidos)) {
        $is_following = true; // Ya sigues a estos usuarios, así que establecer a true
        echo "<div>
                <img src='" . $row_seguido['foto_perfil'] . "' width='50' height='50'> 
                " . $row_seguido['username'] . "
                <div class='usuario'>
                    <button class='follow-btn' data-id-siguiendo='" . $row_seguido['id_usuario'] . "'>
                        " . ($is_following ? 'Dejar de seguir' : 'Seguir') . "
                    </button>
                </div>
              </div>";
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
