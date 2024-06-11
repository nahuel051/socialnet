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
// Se crea un objeto FormData, para recopilar datos 
// de un formulario, usando el formulario actual (this) como parámetro. 
// Este objeto contendrá todos los datos del formulario, 
// incluidos los archivos adjuntos.
                var formData = new FormData(this); 

                $.ajax({
                    type: "POST",
                    url: "publicar.php",
                    data: formData,
                    // No establecer el tipo de contenido automaticamente al enviar los datos
                    //binarios (como archivos)
                    contentType: false, 
                    // No procesar los datos de la forma predeterminada
                    //Es importante para cuando se envian datos binarios ya que no deben ser procesados
                    processData: false, 
                    success: function(response){
                        if(response === "success"){
                            window.location.href = "index.php"; 
                        } else {
                    //Se establece el contenido del elemento HTML con el ID mensaje con la respuesta del servidor
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