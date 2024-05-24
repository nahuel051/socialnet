<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#formulario").submit(function(event){
                event.preventDefault(); 
                
                var formData = new FormData(this); // Crear un objeto FormData con el formulario

                $.ajax({
                    type: "POST",
                    url: "publicar.php",
                    data: formData,
                    contentType: false, // No establecer el tipo de contenido
                    processData: false, // No procesar los datos de la forma predeterminada
                    success: function(response){
                        if(response === "success"){
                            window.location.href = "index.php"; 
                        } else {
                            $("#mensaje").html(response); 
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
<?php include('lateral.php')?>
    <form id="formulario" enctype="multipart/form-data" autocomplete="off" >
        <input type="file" name="imagen">
        <textarea name="descripcion" placeholder="Descripción"></textarea>
        <input type="submit" value="Publicar">
    </form>
    <div id="mensaje"></div>
</body>
</html>