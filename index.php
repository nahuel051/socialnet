<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
$id_usuario_sesion = $_SESSION['registrar']; // El ID del usuario que ha iniciado sesión
if (is_array($id_usuario_sesion)) {
    $id_usuario_sesion = $id_usuario_sesion['id_usuario']; // Ajusta esto según tu estructura de sesión
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
    <a href="cerrar.php">Cerrar Sesion</a>
    <a href="publicar.html">Publicar</a>
    <?php 
    $sql_publicaciones = "SELECT * FROM publicaciones";
    $result_publicaciones = mysqli_query($con, $sql_publicaciones);
    while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
    ?>
    <div class="content-post">
        <img width="150" height="180" src="<?php echo $row_publicacion['imagen']; ?>" alt="Publicacion">
        <?php echo $row_publicacion['descripcion'] ?>
        <div class="comentario">
            <form class="comentar-form" action="comentar.php" method="post">
                <textarea name="comentario" placeholder="Comentar"></textarea>
                <input type="hidden" name="id_publicacion" value="<?php echo $row_publicacion['id_publicacion']; ?>">
                <input type="submit" value="Enviar" name="Comentario">
            </form>
            <div class="mensaje_comentario"></div> <!-- Cambiado a clase -->
        </div>
        <div class="content-comentario" id="comentarios-<?php echo $row_publicacion['id_publicacion']; ?>">
        <?php 
            $id_publicacion = $row_publicacion['id_publicacion'];
            $sql_comentarios = "SELECT c.id_comentario, c.comentario, u.username, c.id_usuario FROM comentarios c JOIN usuarios u ON c.id_usuario = u.id_usuario WHERE c.id_publicacion = $id_publicacion ORDER BY c.fecha_comentario ASC";
            $result_comentarios = mysqli_query($con, $sql_comentarios);
            while ($row_comentario = mysqli_fetch_array($result_comentarios)) {
                echo "<p><strong>" . $row_comentario['username'] . ":</strong> " . $row_comentario['comentario'];
                if ($row_comentario['id_usuario'] == $id_usuario_sesion) {
                    echo " <button class='delete-comentario' data-id-comentario='" . $row_comentario['id_comentario'] . "'>Eliminar</button>";
                }
                echo "</p>";
            }
        ?>
        </div>
        <div class="like">
        <?php
        $sql_cantidad_like = "SELECT count(id_megusta) as total_megusta FROM megusta m JOIN usuarios u ON m.id_usuario = u.id_usuario WHERE m.id_publicacion = $id_publicacion";
        $result_cantidad_like = mysqli_query($con, $sql_cantidad_like);
        if($result_cantidad_like){
            $row_like = mysqli_fetch_assoc($result_cantidad_like);
            $total_like = $row_like['total_megusta'];
        }
        ?>
        <a href="cantidad_like.php">a <?php echo $total_like?> les gusta</a>
        <form action="like.php" method="post">
            <input type="submit" value="Me gusta" name="like">
            <input type="hidden" name="id_publicacion" value="<?php echo $row_publicacion['id_publicacion']; ?>">
        </form>
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
    });
</script>
</body>
</html>
