<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}

$id_usuario_sesion = $_SESSION['registrar'];
if (is_array($id_usuario_sesion)) {
    $id_usuario_sesion = $id_usuario_sesion['id_usuario'];
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
    <?php include('lateral.php')?>
    <?php 
   $sql_publicaciones = "
   SELECT p.*, u.username, u.foto_perfil 
   FROM publicaciones p
   JOIN usuarios u ON p.id_usuario = u.id_usuario
   WHERE p.id_usuario = $id_usuario_sesion
   OR p.id_usuario IN (SELECT id_siguiendo FROM seguidores WHERE id_seguidor = $id_usuario_sesion)
   ORDER BY p.fecha_publicacion DESC";
   
    $result_publicaciones = mysqli_query($con, $sql_publicaciones);
    while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
        $id_publicacion = $row_publicacion['id_publicacion'];

        // Verificar si el usuario ya le ha dado "Me gusta" a esta publicación
        $sql_check_like = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario_sesion'";
        $result_check_like = mysqli_query($con, $sql_check_like);
        $ya_le_gusta = mysqli_num_rows($result_check_like) > 0;
    ?>
    <div class="content-post">
        <a href="<?php echo $row_publicacion['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_publicacion['id_usuario']; ?>">
        <?php echo $row_publicacion['username'] ?>
        </a>
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
            $sql_comentarios = "SELECT c.id_comentario, c.comentario, u.username, c.id_usuario 
                                FROM comentarios c 
                                JOIN usuarios u ON c.id_usuario = u.id_usuario 
                                WHERE c.id_publicacion = $id_publicacion 
                                ORDER BY c.fecha_comentario ASC";
            $result_comentarios = mysqli_query($con, $sql_comentarios);
            while ($row_comentario = mysqli_fetch_array($result_comentarios)) {
                $link = $row_comentario['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_comentario['id_usuario'];
                echo "<div data-id-comentario='" . $row_comentario['id_comentario'] . "'>"; // Encapsular el comentario en un div
                echo "<a href=\"$link\">" . $row_comentario['username'] . ": </a> " . $row_comentario['comentario'];
                if ($row_comentario['id_usuario'] == $id_usuario_sesion) {
                    echo " <button class='delete-comentario' data-id-comentario='" . $row_comentario['id_comentario'] . "'>Eliminar</button>";
                }
                echo "<br>";
                echo "</div>"; // Cerrar el div encapsulador
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
            <a href="cantidad_like.php?id_publicacion=<?php echo $id_publicacion; ?>">a <?php echo $total_like; ?> les gusta</a>
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
                        var nuevoComentario = '<div data-id-comentario="' + response.id_comentario + '">' +
                                              '<a href="' + response.profile_link + '">' + response.username + ': </a>' + response.comentario +
                                              ' <button class="delete-comentario" data-id-comentario="' + response.id_comentario + '">Eliminar</button>' +
                                              '<br></div>';
                        $('#comentarios-' + id_publicacion).append(nuevoComentario);
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
            var comentarioElement = $(this).closest('div[data-id-comentario]');

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
    });
</script>

</body>
</html>
