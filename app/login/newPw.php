<?php
//Connection statement
require_once '../Connections/oirs.php';
//Aditional Functions
require_once '../includes/functions.inc.php';

//capturo el usuario que inicio sesion desde validaLogin
$usuario = $_REQUEST['usuario'];

$query_verProfesion = "SELECT * FROM $MM_oirs_DATABASE.login where USUARIO='$usuario'";
$verProfesion = $oirs->SelectLimit($query_verProfesion) or die($oirs->ErrorMsg());
$totalRows_verProfesion = $verProfesion->RecordCount();

$nivel=$verProfesion->Fields('NIVEL');
?>

<html>
	</head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    </head>

<body><br>
    <div class="row">
    	<div class="col-md-4"></div>

        <div class="col-md-4">
            <div class="card"> 
                <div class="card-body">
                    <h1 align="center"><font color="">Personalizar mi contraseña</font></h1>
                    <br>
                    <h5><font color="">La contraseña debe tener al menos 6 Caracteres</font></h5>
                    <br>
                    <input type="password" id="pass1" class="form-control input-lg" placeholder="Ingrese nueva contraseña">
                    <br>
                    <br>
                    <h5><font color="">Repita la contraseña ingresada</font></h5>
                    <br>
                    <input type="password" id="pass2" class="form-control input-lg" placeholder="Repita contraseña">
                    <br>
                    <div align="center"><button id="btnGuardarPw" class="btn btn-success btn-lg" onclick="fnGuardarPw('<?php echo $usuario ?>','<?php echo $nivel ?>')">Guardar Contraseña</button></div>
                </div>
                <div class="panel-footer"></div>
            </div>
        </div>
        <div class="col-md-4"></div>
    </div>
</body>
</html>


<script>
 function fnGuardarPw(usuario, nivel){
    pass1 = $('#pass1').val();
    pass2 = $('#pass2').val();
if (pass1 != pass2) {
    swal("Oops!", "las contraseñas no coinciden", "warning");
}else{
    ncaracter=pass1.length;
    if (ncaracter < 6) {
        swal("Oops!", "las contraseña tiene menos de 6 caracteres", "warning");
    }else{
    cadena = 'pass1=' + pass1 +
             '&pass2='+ pass2 +
             '&usuario=' +usuario;
        $.ajax({
            type:"post",
            data:cadena,
            url:'guardarPw.php',
            success:function(r){
                if (r == 1) {
                   
                    swal("Genial!", "su contraseña fue Creada, inicie sesion con su nueva contraseña", "success");
                     setTimeout(function(){ location.reload();
                        window.location='../index.php'; }, 4000);
                    
                } else {
                    alertify.error("Fallo el servidor :(");
                }
                
            }
        });
}
}
}
</script>

<style>
  
  /* BASIC */

  html {
    background-color: #56baed;
  }

  body {
    font-family: "Poppins", sans-serif;
    height: 100vh;

    /* Ubicación de la imagen */
    /*background-image: url(../images/fondologin.png);*/
    /* Para dejar la imagen de fondo centrada, vertical y
    horizontalmente */
    background-position: center center;
    /* Para que la imagen de fondo no se repita */
    background-repeat: no-repeat;
    /* La imagen se fija en la ventana de visualización para que la altura de la imagen no supere a la del contenido */
    background-attachment: fixed;
    /* La imagen de fondo se reescala automáticamente con el cambio del ancho de ventana del navegador */
    background-size: cover;
    /* Se muestra un color de fondo mientras se está cargando la imagen
    de fondo o si hay problemas para cargarla */
    background-color: #66999;
  }



</style>