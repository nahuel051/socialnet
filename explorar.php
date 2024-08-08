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
        <input type="text" id="buscar-usuario" placeholder="Buscar usuario...">
        <div id="resultado-busqueda">
            <?php
            // USUARIOS QUE NO ESTEN SEGUIDOS POR EL USUARIO INCIADO
            // WHERE u.id_usuario != '$id_usuario' con esta linea el usuario se excluye de la busqueda
            //AND... asegura que no seleccione usuarios que ya son seguidos por el usuario inciado
            $sql_explorar = "
                SELECT u.id_usuario, u.username, u.foto_perfil 
                FROM usuarios u 
                WHERE u.id_usuario != '$id_usuario' 
                  AND u.id_usuario NOT IN (SELECT id_siguiendo FROM seguidores WHERE id_seguidor = '$id_usuario')
            ";
            //Obtiene una fila de resutados y se almacena en $result_explorar
            $result_explorar = mysqli_query($con, $sql_explorar);
            //El bucle itera sobre cada fila de resultados de la consulta
            while ($row_explorar = mysqli_fetch_array($result_explorar)) {
                //Inicializa la variable como false asumiendo que no sigue al usuario en la fila actual
                $is_following = false;
                //verifocar si $id_usuario sigue en la fila actual a $row_explorar['id_usuario']
                $sql_check_follow = "SELECT * FROM seguidores WHERE id_seguidor = '$id_usuario' AND id_siguiendo = '$row_explorar[id_usuario]'";
                $result_check_follow = mysqli_query($con, $sql_check_follow);
                //Si la consulta devuelve mas de una fila siginifca que el usuario sigue al usuario en fila
                if (mysqli_num_rows($result_check_follow) > 0) {
                //por lo cual is following se establece como true 
                    $is_following = true;
                }
            ?>
            <div class="usuario">
                <img width="50" height="50" src="<?php echo $row_explorar['foto_perfil']; ?>" alt="Foto de perfil">
                <a href="otro_perfil.php?id_usuario=<?php echo $row_explorar['id_usuario']?>"><?php echo $row_explorar['username']; ?></a>
                <button class="follow-btn" data-id-siguiendo="<?php echo $row_explorar['id_usuario']; ?>">
                    <?php echo $is_following ? 'Dejar de seguir' : 'Seguir'; ?>
                </button>
            </div>
            <?php
            }
            ?>
        </div>
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

            // Manejar búsqueda de usuarios
            $('#buscar-usuario').on('keyup', function() {
            //obtiene el valor del campo de busqueda y elimina espacios en blanco al incio y al final
                var terminoBusqueda = $(this).val().trim();
               //Envia solicitud tipo get al archivo
               //pasandole el termino de busqueda como parametro query
               //En caso de exito acutaliza el contenido del elemento con el ID resultado-bsuqeda con la respuesta recibida
                $.ajax({
                    type: 'GET',
                    url: 'buscar_usuarios.php',
                    data: { query: terminoBusqueda },
                    success: function(response) {
                        $('#resultado-busqueda').html(response);
                    }
                });
            });
        });
    </script>
</body>
</html>
