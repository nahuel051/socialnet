<?php
include ('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
}
$id_usuario = $_SESSION['registrar'];
if (is_array($id_usuario)) {
    $id_usuario = $id_usuario['id_usuario'];
}
//Obtiene el valor almacenado en la sesion registrar y lo asigna a la variable
$id_usuario_sesion = $_SESSION['registrar'];
//Verifica si es un array
//Esto puede ocurrir si por ejemplo la sesion registrar tiene mas de un dato
//Si el valor es un array contiene mas informacion que solo el id del usuario
//En este caso el id_usuario esta almacenado dentro del array
// y se le asigna una nueva variable
if (is_array($id_usuario_sesion)) {
    $id_usuario_sesion = $id_usuario_sesion['id_usuario'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="styles/estilo.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container">
        <div class="header">
            <?php include ('lateral.php') ?>
        </div>
        <div class="container-perfil">
            <?php
            //PERFIL DEL USUARIO INICIADO SESION
            $sql = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                ?>
                <div class="superior-perfil">


                    <div class="content-perfil">
                        <img src="<?php echo $row['foto_perfil']; ?>" alt="Foto de perfil">
                        <p><?php echo $row['username'] ?></p>
                        <a href="editar_perfil.php?id_usuario=<?php echo $row['id_usuario'] ?>">Editar perfil</a>
                    </div>
                <?php
            }
            ?>
                <!-- SEGUIDOS Y SEGUIDORES -->
                <div class="seguidores">
                    <h3>Seguidores</h3>
                    <?php
                    // Consulta para obtener el número de seguidores
                    $sql_count_seguidores = "SELECT COUNT(*) as total_seguidores FROM seguidores WHERE id_siguiendo = $id_usuario";
                    $result_count_seguidores = mysqli_query($con, $sql_count_seguidores);
                    //convierte el resultado en un array asociativo, se extrae el valor de la columna
                    //total_seguidores y se almacena en variable $total_seguidores
                    $row_count_seguidores = mysqli_fetch_assoc($result_count_seguidores);
                    $total_seguidores = $row_count_seguidores['total_seguidores'];
                    echo "<a href='cantidad_seguidores.php'>Total de seguidores: $total_seguidores</a>";
                    ?>
                </div>
                <div class="seguidos">
                    <h3>Seguidos</h3>
                    <?php
                    // Consulta para obtener el número de seguidos
                    $sql_count_seguidos = "SELECT COUNT(*) as total_seguidos FROM seguidores WHERE id_seguidor = $id_usuario";
                    $result_count_seguidos = mysqli_query($con, $sql_count_seguidos);
                    //convierte el resultado en un array asociativo, se extrae el valor de la columna
                    //total_seguidos y se almacena en variable $total_seguidos
                    $row_count_seguidos = mysqli_fetch_assoc($result_count_seguidos);
                    $total_seguidos = $row_count_seguidos['total_seguidos'];
                    echo "<a href='cantidad_seguidos.php'>Total de seguidos: $total_seguidos</a>";
                    ?>
                </div>
            </div> <!-- Parte superior-->
            <!-- PUBLICACIONES DEL PERFIL DE USUARIO INICIADO -->
            <?php
            $sql_publicaciones = "SELECT p.*, u.username FROM publicaciones p JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE p.id_usuario = $id_usuario ORDER BY p.fecha_publicacion DESC";
            ;
            $result_publicaciones = mysqli_query($con, $sql_publicaciones);
            while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
                $id_publicacion = $row_publicacion['id_publicacion'];

                // Verificar si el usuario ya le ha dado "Me gusta" a esta publicación
                $sql_check_like = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario'";
                $result_check_like = mysqli_query($con, $sql_check_like);
                $ya_le_gusta = mysqli_num_rows($result_check_like) > 0;
                ?>
                <div class="content-post">
                    <div class="superior-post">
                        <h2><?php echo $row_publicacion['username'] ?></h2>
                        <div><a href="delete.php?id_publicacion=<?php echo
                            $row_publicacion['id_publicacion'] ?>"><i class="fa-solid fa-trash"></i></a>
                            <a href="editar_publicacion.php?id_publicacion=<?php echo $row_publicacion['id_publicacion'] ?>"><i
                                    class="fa-solid fa-pen-to-square"></i></a>
                        </div>
                    </div> <!--Superior post -->
                    <img width="150" height="180" src="<?php echo $row_publicacion['imagen']; ?>" alt="Publicacion">
                    <?php echo $row_publicacion['descripcion'] ?>
                    <div class="like">
                        <?php
                        $sql_cantidad_like = "SELECT count(id_megusta) as total_megusta FROM megusta WHERE id_publicacion = $id_publicacion";
                        $result_cantidad_like = mysqli_query($con, $sql_cantidad_like);
                        if ($result_cantidad_like) {
                            $row_like = mysqli_fetch_assoc($result_cantidad_like);
                            $total_like = $row_like['total_megusta'];
                        }
                        ?>
                        <a class="like-index" href="cantidad_like.php">a <?php echo $total_like ?> les gusta</a>
                        <input type="checkbox" class="like-checkbox"
                            data-id-publicacion="<?php echo $row_publicacion['id_publicacion']; ?>" <?php echo $ya_le_gusta ? 'checked' : ''; ?>>
                    </div> <!--Like -->
                    <!-- COMENTARIOS -->
                    <!-- Contenedor para comentar -->
                    <div class="comentario">
                        <form class="comentar-form" action="comentar.php" method="post">
                            <div class="section-coment">
                                <textarea name="comentario" placeholder="Comentar"></textarea>
                                <!-- Campo de entrada oculto y dentro de value refiere al id de la publicacion actual
        obtenido por el bucle while.
        Se utiliza para enviar el id de la publicacion al archivo comentar.php cuando se envia el formulario
        El id de publicacion es importatnte para que comentar.php sepa que la publicacion se refiere al comentario enviado -->
                                <input type="hidden" name="id_publicacion"
                                    value="<?php echo $row_publicacion['id_publicacion']; ?>">
                                <!-- <input type="submit" value="Enviar" name="Comentario"> -->
                                <button type="submit" name="Comentario" class="btn-icon">
                                    <i class="material-icons">&#xe163;</i>
                                </button>
                            </div>
                        </form>
                        <div class="mensaje_comentario"></div>
                    </div>
                    <!-- Contenedor de comentarios  -->
                    <!-- El atributo id se establece utilizando php para que cada div tenga un id unico basado
        en el id de la publicacion, lo que permite identificar de manera unica la seccion de comentarios
        asociada a cada publicacion en la pagina. -->
                    <div class="content-comentario" id="comentarios-<?php echo $row_publicacion['id_publicacion']; ?>">
                        <div class="coment-cont">
                            <?php
                            //WHERE c.id_publicacion = $id_publicacion: solo selecciona los comentarios
                            //donde el id de la publicacion sea igual que el id de la publicacion especifica 
                            //para el cual estan recuperados los comentarios, y esta representado por la variable $id_publicacion
                            $sql_comentarios = "SELECT c.id_comentario, c.comentario, u.username, c.id_usuario 
                                FROM comentarios c 
                                JOIN usuarios u ON c.id_usuario = u.id_usuario 
                                WHERE c.id_publicacion = $id_publicacion 
                                ORDER BY c.fecha_comentario ASC";
                            $result_comentarios = mysqli_query($con, $sql_comentarios);
                            while ($row_comentario = mysqli_fetch_array($result_comentarios)) {
                                $link = $row_comentario['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_comentario['id_usuario'];
                                //Se crea un div para encapsular el cometario.
                                //Utiliza el atributo data-id-cometario para almacenar el id unico del comentario
                                //que proviene de la columna id_comentario del array $row_comentario
                                //Es util para identificar el comentario de forma unica, para por ejemplo eliminarlo posteriormente
                                echo "<div data-id-comentario='" . $row_comentario['id_comentario'] . "'>";
                                //imprime el nombre del usuario que comento seguido de dos puntos. 
                                //tambien imprime el comentario.
                                echo "<a class='link_coment' href=\"$link\">" . $row_comentario['username'] . ": </a> " . $row_comentario['comentario'];
                                //Si el comentario fue escrito por el usuario actual
                                //se muestra el boton de eliminar
                                //tiene un atributo data-id-comentario que almacena el id del comentario.
                                //Esto facilita la identificacion del comentario que se desea eliminar cuando se hace clic
                                if ($row_comentario['id_usuario'] == $id_usuario_sesion) {
                                    echo " <button class='delete-comentario' data-id-comentario='" . $row_comentario['id_comentario'] . "'><i class='fa-solid fa-trash'></i></button>";
                                }
                                echo "<br>";
                                //se cierra el div que encapsula todo el contenido del comentario
                                echo "</div>";
                            }
                            ?>
                        </div> <!-- coment-cont -->
                    </div>
                </div>
                <?php
            }
            ?>
        </div> <!-- CONTAINER PERFIL -->
    </div> <!-- CONTAINER -->
    <script>
        $(document).ready(function () {
            $('.comentar-form').on('submit', function (event) {
                event.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                var mensajeComentario = form.next('.mensaje_comentario'); // Selecciona el contenedor de mensaje de error relativo al formulario

                $.ajax({
                    type: 'POST',
                    url: 'comentar.php',
                    data: formData,
                    dataType: 'json', // Especificar que la respuesta es JSON
                    success: function (response) {
                        if (response.success) {
                            var id_publicacion = form.find('input[name="id_publicacion"]').val();

                            
                            // $('#comentarios-' + id_publicacion).append('<p><strong>' + response.username + ':</strong> ' + response.comentario + '</p>');
                            // form.find('textarea[name="comentario"]').val('');
                            // mensajeComentario.html(''); // Limpiar mensaje de error
                            var nuevoComentario = '<div data-id-comentario="' + response.id_comentario + '">' +
                                '<a href="' + response.profile_link + ' " class="link_coment">' + response.username + ': </a>' + response.comentario +
                                ' <button class="delete-comentario" data-id-comentario="' + response.id_comentario + '"><i class="fa-solid fa-trash"></i></button>' +
                                '<br></div>';
                                $('#comentarios-' + id_publicacion + ' .coment-cont').append(nuevoComentario);
                                form.find('textarea[name="comentario"]').val('');
                                mensajeComentario.html('');

                        } else {
                            mensajeComentario.html(response.error);
                        }
                    }
                });
            });

            // Manejar evento de cambio en el checkbox de Me gusta
            $(document).on('change', '.like-checkbox', function () {
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
                    success: function (response) {
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
            $(document).on('click', '.delete-comentario', function () {
                var idComentario = $(this).data('id-comentario');
                var comentarioElement = $(this).closest('div[data-id-comentario]');
                $.ajax({
                    type: 'POST',
                    url: 'delete-comentario.php',
                    data: { id_comentario: idComentario },
                    success: function (response) {
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
    <script src="script.js"></script>
</body>

</html>