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
    <title>Seguidos</title>
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
//VARIABLE PARA SEGUIMIENTO
//asigna el valor true a la variable, el motivo es que se listan los usuarios
//que el usuario iniciado sigue, por lo tanto true por definicion, el usuario inciado
//ya sigue todos los usuarios que estan mostrando en la lista
        $is_following = true; 
    ?>
        <div class="usuario">
        <img width="50" height="50" src="<?php echo $row_seguido['foto_perfil']; ?>" alt="Foto de perfil">
        <a href="otro_perfil.php?id_usuario=<?php echo $row_seguido['id_usuario']?>"><?php echo $row_seguido['username']; ?></a>
<!-- BOTON DE SEGUIMIENTO
agrega un atributo data-id-siguiendo al boton que contiene id_usuario del usuario que esta id-siguiendo
es util para identificar a que usuario se refiere la accion cuando se presiona el boton -->
        <button class="follow-btn" data-id-siguiendo="<?php echo $row_seguido['id_usuario']; ?>">
<!-- Expresion condicional para determinar el texto del boton
si es true el boton mostrar dejar de seguir
si es false el boton mostrar seguir -->
        <?php echo $is_following ? 'Dejar de seguir' : 'Seguir'; ?>
        </button>
        </div>
   <?php }
    ?>
</div>

<script>
    $(document).ready(function() {
        // Manejar seguimiento de usuarios
        $('.follow-btn').on('click', function() {
            //button refiere al boton clicado
            var button = $(this);
            //id_siguiendo obtiene el id del usuario a seguir o dejar de seguir
            //desde el atributo data-id-siguiendo del boton
            var id_siguiendo = button.data('id-siguiendo');
            //action determina si la accion es seguir o dejar de seguir basandose en el texto
            var action = button.text().trim() === 'Seguir' ? 'seguir' : 'dejar_de_seguir';

            //se envia la solicitud POST a seguir.php o dejar_de_seguir.php
            //los datos enviados incluyen el id del usuario a seguir o dejar
           //en caso de exito el texto del boton se actualiza
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
