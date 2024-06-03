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
    <title>Perfil</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include('lateral.php') ?>
    <?php 
    $sql = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
    $result = mysqli_query($con, $sql);
    while($row = mysqli_fetch_array($result)){
    ?>
        <div class="content-perfil">
            <?php echo $row['username'] ?>
            <img width="150" height="180" src="<?php echo $row['foto_perfil']; ?>" alt="Foto de perfil">
            <a href="editar_perfil.php?id_usuario=<?php echo $row['id_usuario']?>">Editar</a>
        </div>
    <?php       
    }
    ?>
    <hr>

    <a href="explorar.php">Explorar</a>
    <div class="seguidores">
        <h3>Seguidores</h3>
        <?php
        // Consulta para obtener el número de seguidores
        $sql_count_seguidores = "SELECT COUNT(*) as total_seguidores FROM seguidores WHERE id_siguiendo = $id_usuario";
        $result_count_seguidores = mysqli_query($con, $sql_count_seguidores);
        $row_count_seguidores = mysqli_fetch_assoc($result_count_seguidores);
        $total_seguidores = $row_count_seguidores['total_seguidores'];
        echo "<a href='cantidad_seguidores.php'>Total de seguidores: $total_seguidores</a>";
        ?>
    </div>

    <hr>
    <div class="seguidos">
        <h3>Seguidos</h3>
        <?php
        // Consulta para obtener el número de seguidos
        $sql_count_seguidos = "SELECT COUNT(*) as total_seguidos FROM seguidores WHERE id_seguidor = $id_usuario";
        $result_count_seguidos = mysqli_query($con, $sql_count_seguidos);
        $row_count_seguidos = mysqli_fetch_assoc($result_count_seguidos);
        $total_seguidos = $row_count_seguidos['total_seguidos'];
        echo "<a href='cantidad_seguidos.php'>Total de seguidos: $total_seguidos</a>";
        ?>
    </div>
    <hr>
    <?php 
    $sql_publicaciones = "SELECT p.*, u.username FROM publicaciones p JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE p.id_usuario = $id_usuario";
    $result_publicaciones = mysqli_query($con, $sql_publicaciones);
    while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
        $id_publicacion = $row_publicacion['id_publicacion'];

        // Verificar si el usuario ya le ha dado "Me gusta" a esta publicación
        $sql_check_like = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
        $result_check_like = mysqli_query($con, $sql_check_like);
        $ya_le_gusta = mysqli_num_rows($result_check_like) > 0;
    ?>
    <div class="content-post">
        <h2><?php echo $row_publicacion['username'] ?></h2>
        <img width="150" height="180" src="<?php echo $row_publicacion['imagen']; ?>" alt="Publicacion">
        <?php echo $row_publicacion['descripcion'] ?>
        <div class="comentario">
            <form class="comentar-form" action="comentar.php" method="post">
                <textarea name="comentario" placeholder="Comentar"></textarea>
                <input type="hidden" name="id_publicacion" value="<?php echo $row_publicacion['id_publicacion']; ?>">
                <input type="submit" value="Enviar" name="Comentario">
            </form>
            <div class="mensaje_comentario"></div>
        </div>
        <div class="content-comentario" id="comentarios-<?php echo $row_publicacion['id_publicacion']; ?>">
        <?php 
            $sql_comentarios = "SELECT c.id_comentario, c.comentario, u.username, c.id_usuario FROM comentarios c JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.id_publicacion = $id_publicacion ORDER BY c.fecha_comentario ASC";
            $result_comentarios = mysqli_query($con, $sql_comentarios);
            while ($row_comentario = mysqli_fetch_array($result_comentarios)) {
                $link = $row_comentario['id_usuario'] == $id_usuario ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_comentario['id_usuario'];
                echo "<a href=\"$link\">" . $row_comentario['username'] . ": </a> " . $row_comentario['comentario'];                if ($row_comentario['id_usuario'] == $id_usuario) {
                    echo " <button class='delete-comentario' data-id-comentario='" . $row_comentario['id_comentario'] . "'>Eliminar</button>";
                }
                echo "</p>";
            }
        ?>
        </div>
        <div class="like">
            <?php
            $sql_cantidad_like = "SELECT count(id_megusta) as total_megusta FROM megusta WHERE id_publicacion = $id_publicacion";
            $result_cantidad_like = mysqli_query($con, $sql_cantidad_like);
            if ($result_cantidad_like) {
                $row_like = mysqli_fetch_assoc($result_cantidad_like);
                $total_like = $row_like['total_megusta'];
            }
            ?>
            <a href="cantidad_like.php">a <?php echo $total_like ?> les gusta</a>
            <input type="checkbox" class="like-checkbox" data-id-publicacion="<?php echo $row_publicacion['id_publicacion']; ?>" <?php echo $ya_le_gusta ? 'checked' : ''; ?>>
        </div>
    </div>
    <?php
    }
    ?>
    <script>
        $(document).ready(function() {
            $('.comentar-form').on('submit', function(event) {
                event.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                var mensajeComentario = form.next('.mensaje_comentario'); // Selecciona el contenedor de mensaje de error relativo al formulario

                $.ajax({
                    type: 'POST',
                    url: 'comentar.php',
                    data: formData,
                    dataType: 'json', // Especificar que la respuesta es JSON
                    success: function(response) {
                        if (response.success) {
                            var id_publicacion = form.find('input[name="id_publicacion"]').val();
                            $('#comentarios-' + id_publicacion).append('<p><strong>' + response.username + ':</strong> ' + response.comentario + '</p>');
                            form.find('textarea[name="comentario"]').val('');
                            mensajeComentario.html(''); // Limpiar mensaje de error
                        } else {
                            mensajeComentario.html(response.error);
                        }
                    }
                });
            });

            // Manejar evento de cambio en el checkbox de Me gusta
            $(document).on('change', '.like-checkbox', function() {
                var checkbox = $(this);
                var idPublicacion = checkbox.data('id-publicacion');
                var isChecked = checkbox.prop('checked');

                $.ajax({
                    type: 'POST',
                    url: 'like.php',
                    data: {
                        id_publicacion: idPublicacion,
                        like_action: isChecked ? 'add' : 'remove' // Indicar si se agrega o se elimina el Me gusta
                    },
                    success: function(response) {
                        // Actualizar la UI según sea necesario
                        var totalLikesElement = checkbox.closest('.like').find('a');
                        var totalLikesText = totalLikesElement.text();
                        var totalLikes = parseInt(totalLikesText.match(/\d+/)[0]);

                        if (isChecked) {
                            // Me gusta agregado
                            totalLikesElement.text('a ' + (totalLikes + 1) + ' les gusta');
                        } else {
                            // Me gusta eliminado
                            totalLikesElement.text('a ' + (totalLikes - 1) + ' les gusta');
                        }
                    }
                });
            });
            
            // Manejar eliminación de comentarios
            $(document).on('click', '.delete-comentario', function() {
                var idComentario = $(this).data('id-comentario');
                var comentarioElement = $(this).closest('p');

                $.ajax({
                    type: 'POST',
                    url: 'delete-comentario.php',
                    data: { id_comentario: idComentario },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.success) {
                            comentarioElement.remove();
                        } else {
                            alert(data.error);
                        }
                    }
                });
            });

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
