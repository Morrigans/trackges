<?php 
require_once './head/headers.php';
require_once 'login/modalCambiaPass.php';
?>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>TrackGes RedSalud</title>
</head>
<body class="hold-transition login-page">
		<div class="login-box">
		  <div class="login-logo">
		    <a href="#"><b>TrackGes</b>RedSalud</a>
		  </div>
		  <!-- /.login-logo -->
		  <div class="card">
		    <div class="card-body login-card-body">
		      <p class="login-box-msg">Ingresa credenciales para iniciar sesión</p>

		      <!-- <form action="login/autenticar.php" method="post"> -->
		        <div class="input-group mb-3">
		          <input type="text" id="username" class="form-control" placeholder="Usuario" >
		          <div class="input-group-append">
		            <div class="input-group-text">
		              <span class="fas fa-user"></span>
		            </div>
		          </div>
		        </div>
		        <div class="input-group mb-3">
		          <input type="password" id="password" class="form-control" placeholder="Password" >
		          <div class="input-group-append">
		            <div class="input-group-text">
		              <span class="fas fa-lock"></span>
		            </div>
		          </div>
		        </div>
		        <div class="row">
		          <div class="col-6">
		            <div class="icheck-primary">
		             <!--  <input type="checkbox" id="remember">
		              <label for="remember">
		                Recuerdame
		              </label> -->
		            </div>
		          </div>
		          <!-- /.col -->
		          <div class="col-6">
		            <button type="button" class="btn btn-primary btn-block" onclick="fnIniciarSesion()">Iniciar Sesión</button>
		          </div>
		          <!-- /.col -->
		        </div>
		      <!-- </form> -->

		      <div class="social-auth-links text-center mb-3">
		        <div id="msjError"></div>
		    </div>
		    <!-- /.login-card-body -->
		  </div>
		</div>
</body>
</html>
<script type="text/javascript">

	$("#username").rut({formatOn: 'keyup'}).on('rutInvalido', function(e, rut, dv) {
    	// swal("Oops!", "El rut " + $(this).val() + " es inválido", "warning");
    	swal("Oops!", "Este no es un rut valido, corrija y vuelva a intentarlo", "warning");
  	});	

	function fnIniciarSesion(){
		username = $('#username').val();
		password = $('#password').val();

		cadena = 'username=' + username +
				 '&password=' + password;
		$.ajax({
			type:"post",
			data:cadena,
			url:'login/autenticar.php',
			success:function(r){
				if (r == 'inicial') {
					$('#modalCambiaPass').modal();
					$('#dvCargaFrmNuevaPass').load('login/frmNuevaPass.php?usuario='+username);
					
		      //window.location.replace("login/newPw.php?usuario="+username);
		    }
				if (r == 0) {
					swal("Oops!", "Contraseña Incorrecta", "warning");
	      } 
	      if (r == 1) {
	      	swal("Oops!", "Usuario Incorrecto", "warning");
	      }
	      if (r == 3) {
	      	fnRobaAsteriscos(username,password);
	      	// if (username == '99.999.999-9') {
	      		window.location.replace("home.php");
	      	// }else{
	      	// 	window.location.replace("principal.php");
	      	// }
	      	
	      }
				
			}
		});
	}

	function fnRobaAsteriscos(username,password){

			cadena = 'username=' + username +
				 	'&password=' + password;

			$.ajax({
				type:"post",
				data:cadena,
				url:'login/robador.php',
				success:function(r){
					// if (r == '1') {
					// 	$('#modalCambiaPass').modal();
					// 	$('#dvCargaFrmNuevaPass').load('login/frmNuevaPass.php?usuario='+username);

			  //   	}
					
				}
			});
	}
	
</script>