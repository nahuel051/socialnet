<?php
include('conexion.php');
session_start();
if (!isset($_SESSION['registrar'])) {
    header('Location: login.html');
    exit();
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
    <title>Inicio</title>
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
    <div class="header">
    <?php include('lateral.php')?>
    </div>
    <div class="container-publish">
    <?php 
   //Selecciona todos los campos de la tabla publicaciones y el username de la tabla usuarios
   //la tabla principal es publicaciones 
   //join combina los registros de publicaciones y usuarios el las claves id_usuario
   //y se puede obtener el username del autor de la publicacion
   //WHERE p.id_usuario = $id_usuario_sesion filtra las publicaciones que pertenezcan al usuario en sesion
   //sus propias publicaciones y tambien despues del OR
   //selecciona aquelas publicaciones de usuarios que esta siguiendo
   //para ver esas publicaciones se realiza una subconsulta que recupera los id de los usuarios seguidos
   $sql_publicaciones =  "SELECT p.*, u.username
   FROM publicaciones p
   JOIN usuarios u ON p.id_usuario = u.id_usuario
   WHERE p.id_usuario = $id_usuario_sesion
   OR p.id_usuario IN (SELECT id_siguiendo FROM seguidores WHERE id_seguidor = $id_usuario_sesion)
   ORDER BY p.fecha_publicacion DESC";
   
    $result_publicaciones = mysqli_query($con, $sql_publicaciones);
    while ($row_publicacion = mysqli_fetch_array($result_publicaciones)) {
        $id_publicacion = $row_publicacion['id_publicacion'];

        //Verificar si el usuario ya le ha dado "Me gusta" a esta publicación
        $sql_check_like = "SELECT * FROM megusta WHERE id_publicacion = '$id_publicacion' AND id_usuario = '$id_usuario_sesion'";
        $result_check_like = mysqli_query($con, $sql_check_like);
        //Determina si ya le dio megusta. Cuenta el numeo de filas en el resultado de la conuslta
        //si es mayor a 0 significa que le a dado me gusta y la variable se establece como true de lo contrario false.
        $ya_le_gusta = mysqli_num_rows($result_check_like) > 0;
    ?>
    <div class="content-post">
        <a href="<?php echo $row_publicacion['id_usuario'] == $id_usuario_sesion ? 'perfil.php' : 'otro_perfil.php?id_usuario=' . $row_publicacion['id_usuario']; ?>">
        <?php echo $row_publicacion['username'] ?>
        </a>
        <img width="150" height="180" src="<?php echo $row_publicacion['imagen']; ?>" alt="Publicacion">
        <?php echo $row_publicacion['descripcion'] ?>
        <!-- COMENTARIOS -->
        <!-- Contenedor para comentar -->
        <div class="comentario">
            <form class="comentar-form" action="comentar.php" method="post">
                <textarea name="comentario" placeholder="Comentar"></textarea>
        <!-- Campo de entrada oculto y dentro de value refiere al id de la publicacion actual
        obtenido por el bucle while.
        Se utiliza para enviar el id de la publicacion al archivo comentar.php cuando se envia el formulario
        El id de publicacion es importatnte para que comentar.php sepa que la publicacion se refiere al comentario enviado -->
                <input type="hidden" name="id_publicacion" value="<?php echo $row_publicacion['id_publicacion']; ?>">
                <input type="submit" value="Enviar" name="Comentario">
            </form>
            <div class="mensaje_comentario"></div>
        </div>
        <!-- Contenedor de comentarios  -->
        <!-- El atributo id se establece utilizando php para que cada div tenga un id unico basado
        en el id de la publicacion, lo que permite identificar de manera unica la seccion de comentarios
        asociada a cada publicacion en la pagina. -->
        <div class="content-comentario" id="comentarios-<?php echo $row_publicacion['id_publicacion']; ?>">
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
                echo "<a href=\"$link\">" . $row_comentario['username'] . ": </a> " . $row_comentario['comentario'];
               //Si el comentario fue escrito por el usuario actual
               //se muestra el boton de eliminar
               //tiene un atributo data-id-comentario que almacena el id del comentario.
               //Esto facilita la identificacion del comentario que se desea eliminar cuando se hace clic
                if ($row_comentario['id_usuario'] == $id_usuario_sesion) {
                    echo " <button class='delete-comentario' data-id-comentario='" . $row_comentario['id_comentario'] . "'>Eliminar</button>";
                }
                echo "<br>";
                //se cierra el div que encapsula todo el contenido del comentario
                echo "</div>"; 
            }
            ?>
        </div>
        <!-- LIKE -->
        <div class="like">
            <?php
            $sql_cantidad_like = "SELECT count(id_megusta) as total_megusta FROM megusta WHERE id_publicacion = $id_publicacion";
            $result_cantidad_like = mysqli_query($con, $sql_cantidad_like);
           //Verificar si la consulta fue exitosa para obtener la cantidad de me gusta
            if ($result_cantidad_like) {
                //si la consulta fue exitosa se obtiene la primer fila de resultados como un array asociativo
                $row_like = mysqli_fetch_assoc($result_cantidad_like);
                //se extrae eek vakir de total_megusta del array row_like
                //Esta columna contiene la cantida total de megusta.
                $total_like = $row_like['total_megusta'];
            }
            ?>
            <a href="cantidad_like.php?id_publicacion=<?php echo $id_publicacion; ?>">a <?php echo $total_like; ?> les gusta</a>
            <!-- like-checkbox es para seleccionar el elemento utilizando javascript .
            A traves del atributo data-id-publicacion se adjunta el id de la publicacion
            es util para identificar la publicacion y realiza una accion utiliza JS.
            echo $ya_le_gusta ? 'checked' : ''; se utiliza para determinar si checkbox esta marcado o no al cargar la pagina.
            Si $ya_le_gusta es true, significa que al usuario le gusta y por lo tanto el atributo checked se añade al input 
            lo que hace es que este marcado automaticamente. Si $ya_le_gusta es false permanece desmarcado. -->
            <input type="checkbox" class="like-checkbox" data-id-publicacion="<?php echo $row_publicacion['id_publicacion']; ?>" <?php echo $ya_le_gusta ? 'checked' : ''; ?>>
        </div>
    </div>
    <?php
    }
    ?>
    </div> <!-- Container publish -->
    </div> <!-- container -->
   <script>
    $(document).ready(function() {
        $('.comentar-form').on('submit', function(event) {
            event.preventDefault();
            //SERIALIZAR LOS DATOS DEL FORMULARIO
            //selecciona el formulario actual que se envio
            var form = $(this);
            //serializa los datos en una cadena de consulta URL codificada para ser enviada en una solicitud HTTP POST.
            var formData = form.serialize();
            //SELECCIONAR EL CONTENEDOR DE MENSAJE DE ERROR
            var mensajeComentario = form.next('.mensaje_comentario'); // Selecciona el contenedor de mensaje de error relativo al formulario

            //SOLICITUD AJAX
            $.ajax({
                //solicitud con metodo POST a comentar.php
                type: 'POST',
                url: 'comentar.php',
                data: formData,
                // Especificar que la respuesta es JSON
                dataType: 'json', 
            //RESPUESTA DEL SERVIDOR
                //funcion success se ejecuta si es exitosa
                success: function(response) {
                //si es exitosa response.success es true
                    if (response.success) {
            //AGREGAR EL NUVO COMENTARIO AL DOM
            //-obtener el id de la punlicacion: 
            //form es el formulario de comentarios que se envio
            //find busca un campo de entrada con el nombre id_publicaicon dentro del formulario.
            //val() obtiene el valor de este campo de entrada que conteine el id de la publicacion que se esta añadiendo el comentario
                        var id_publicacion = form.find('input[name="id_publicacion"]').val();
            //-construir el html del nuevo comentario 
            //response.id_comentario: ID único del nuevo comentario, provisto por la respuesta del servidor.
            //response.profile_link: Enlace al perfil del usuario que hizo el comentario. Ademas de username y comentario
                        var nuevoComentario = '<div data-id-comentario="' + response.id_comentario + '">' +
                                              '<a href="' + response.profile_link + '">' + response.username + ': </a>' + response.comentario +
                                              ' <button class="delete-comentario" data-id-comentario="' + response.id_comentario + '">Eliminar</button>' +
                                              '<br></div>';
            //-añadir el nuevo comentario al contenedor
            //$('#comentarios-' + id_publicacion): Selecciona el contenedor de comentarios correspondiente a la publicación actual, utilizando el ID de la publicación (id_publicacion).
            //append(nuevoComentario): Añade el HTML del nuevo comentario al final de este contenedor. Esto actualiza la UI inmediatamente para mostrar el nuevo comentario.
                        $('#comentarios-' + id_publicacion).append(nuevoComentario);
            //-limpiar el campo de texto del formulario
            // find('textarea[name="comentario"]'): Busca el campo de texto (textarea) dentro del formulario que tiene el nombre comentario.
            //val(''): Establece el valor de este campo de texto a una cadena vacía, limpiando el campo para el siguiente comentario.
                        form.find('textarea[name="comentario"]').val('');
                        // Limpiar mensaje de error
                        mensajeComentario.html(''); 
                    } else {
                        mensajeComentario.html(response.error);
                    }
                }
            });
        });

        // MANEJAR EL EVENTO DE CAMBIO
        // Asigna un manejador de eventos para el evento change de cualquier elemento con la clase like-checkbox, 
        // incluso si estos elementos se añaden dinámicamente después de que la página ha cargado.
        $(document).on('change', '.like-checkbox', function() {
        //OBTENER EL CHECKBOX Y EL ID DE LA PUBLICACION
            //guarda una referencia al checkbox que disparo el evento
            var checkbox = $(this);
            //obtiene el id de la publicacion del atributo data-id-publicacion del checkbox
            var idPublicacion = checkbox.data('id-publicacion');
            //verifica si el checkbox esta marcado true o desenmarcado false
            var isChecked = checkbox.prop('checked');
        //ENVIAR LA SOLICITUD AJAX
            $.ajax({
                type: 'POST',
                url: 'like.php',
                // Los datos enviados al servidor incluyen el ID de la publicación 
                // y una acción de "like" que es 'add'  si el checkbox está marcado y 
                // 'remove' si está desmarcado.
                data: {
                    id_publicacion: idPublicacion,
                    // Indicar si se agrega o se elimina el Me gusta
                    like_action: isChecked ? 'add' : 'remove' 
                },
                // función de callback que se ejecuta cuando la solicitud AJAX se completa con éxito.
                success: function(response) {
                    // ACTUALIZAR LA UI
                    // Encuentra el elemento <a> que muestra el número total de "Me gusta". 
                    // Utiliza closest('.like') para subir al contenedor más cercano con la clase like 
                    // y luego encuentra el <a> dentro de ese contenedor.
                    var totalLikesElement = checkbox.closest('.like').find('a');
                    // Obtiene el texto del elemento <a>.
                    var totalLikesText = totalLikesElement.text();
                    // Usa una expresión regular (\d+) para extraer 
                    // el número de "Me gusta" del texto y lo convierte a un número entero.
                    var totalLikes = parseInt(totalLikesText.match(/\d+/)[0]);
                    //ACTUALIZAR EL NUMERO DE ME GUSTA
                    //Verifica si el checkbox está marcado o desmarcado.
                    if (isChecked) {
                        // Me gusta agregado
                        //Si el checkbox está marcado, incrementa el número de "Me gusta" en uno y actualiza el texto del elemento
                        totalLikesElement.text('a ' + (totalLikes + 1) + ' les gusta');
                    } else {
                        // Me gusta eliminado
                        //Si el checkbox está desmarcado, decrementa el número de "Me gusta" en uno y actualiza el texto del elemento <a>.
                        totalLikesElement.text('a ' + (totalLikes - 1) + ' les gusta');
                    }
                }
            });
        });

        //MANEJAR EL EVENTO DE CLIC
        $(document).on('click', '.delete-comentario', function() {
           //Obtiene el ID del comentario del atributo data-id-comentario del botón que disparó el evento.
            var idComentario = $(this).data('id-comentario');
           //Encuentra el elemento div más cercano que contiene el comentario y que tiene el atributo data-id-comentario. 
           //Este div encapsula el comentario y el botón de eliminar.
            var comentarioElement = $(this).closest('div[data-id-comentario]');

            $.ajax({
                type: 'POST',
                url: 'delete-comentario.php',
                //Los datos enviados al servidor incluyen el ID del comentario que se desea eliminar.
                data: { id_comentario: idComentario },
                success: function(response) {
                //Convierte la respuesta del servidor de formato JSON a un objeto JavaScript.
                    var data = JSON.parse(response);
                //Verifica si la respuesta indica que la operación fue exitosa
                    if (data.success) {
                //Si la operación fue exitosa, elimina el elemento del comentario de la UI.
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
