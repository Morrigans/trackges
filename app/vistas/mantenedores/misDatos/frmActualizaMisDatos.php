<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
//require_once 'fnPass.js';

$rutPro = $_REQUEST['usuario'];

$query_qrPro= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO='$rutPro'";
$qrPro = $oirs->SelectLimit($query_qrPro) or die($oirs->ErrorMsg());
$totalRows_qrPro = $qrPro->RecordCount();

// $nomPro= $qrPro->Fields('NOMBRE');
// $mail= $qrPro->Fields('MAIL');
// $fono= $qrPro->Fields('FONO');
// $direccion= $qrPro->Fields('DIRECCION');

?>
<head>
	<!-- <script src="vistas/mantenedores/misDatos/fnPass.js" ></script> -->
</head>
<form id="frmActualizaMisDatos">
	<div class="card card-warning">
	  	<div class="card-header">
	    	<h3 class="card-title">Actualizar mis datos</h3>
	  	</div>
	  	<div class="card-body">
		  	<div class="row">				  
		  		<input type="hidden" class="form-control input-sm" name="hdRutMisDatosEd" id="hdRutMisDatosEd" value="<?php echo $rutPro ?>" />

			    <div class="input-group mb-3 col-sm-12">
			      <div class="input-group-prepend">
			        <span class="input-group-text">Nombre</span>
			      </div>
			      <input type="text" class="form-control input-sm" name="nomProfesionalEd" id="nomProfesionalEd"/>
			    </div>

			    <div class="input-group mb-3 col-sm-6">
			      <div class="input-group-prepend">
			        <span class="input-group-text">Correo</span>
			      </div>
			      <input type="text" class="form-control input-sm" name="correoProfesionalEd" id="correoProfesionalEd"/>
			    </div>

			    <div class="input-group mb-3 col-sm-6">
			      <div class="input-group-prepend">
			        <span class="input-group-text">Teléfono</span>
			      </div>
			      <input type="text" class="form-control input-sm" name="fonoProfesionalEd" id="fonoProfesionalEd"/>
			    </div>

			    <div class="input-group mb-3 col-sm-6">
			       	<div class="input-group-prepend">
			        	<span id="spPassword" class="input-group-text">Nueva contraseña</span>
			      	</div>
			      	<input type="password" class="form-control input-sm" name="nuevaPassEd" id="nuevaPassEd"/>
			      	<br>
			      	<span id="msjPass"></span>
			      	<!-- REGLAS DE VALIDACION DE CONTRASEÑA -->
			      	<div class="input-group mb-3 col-sm-12">			      		
			      	    <ul>
			      	      	<li id="mayus">1 Mayúscula mínimo</li>
			      	      	<li id="tamanio">6 Caractéres mínimo</li>
			      	    </ul>
			      	</div>
			    </div>

			    <div class="input-group mb-3 col-sm-6">
			      	<div class="input-group-prepend">
			        	<span id="spPassword2" class="input-group-text">Confirmar contraseña</span>
			      	</div>
			      	<input type="password" class="form-control input-sm" name="confirmaPassEd" id="confirmaPassEd"/>
			      	<br>
			      	<!-- REGLAS DE VALIDACION DE CONTRASEÑA -->
			      	<span id="msjPass2"></span>
			      	<div class="input-group mb-3 col-sm-12">			      		
			      	    <ul>
			      	      	<li id="mayus2">1 Mayúscula mínimo</li>
			      	      	<li id="tamanio2">6 Caractéres mínimo</li>
			      	    </ul>
			      	</div>
			    </div>

			</div>
	   	</div>
	   	<div class="card-footer row col-12">

	   		<div class="input-group col-sm-4">
	   		    <button type="submit" class="btn btn-warning col-12">Guardar cambios</button>
	   		</div>

	   		<div class="input-group col-sm-4">
	   		</div>

	   		<div class="input-group col-sm-4">
	   			<button type="button" id="btnCambiaMisDatos" class="btn btn-outline-warning col-12" data-dismiss="modal">Cerrar</button>
	   		</div>
		</div>
	</div>
</form>

<script type="text/javascript">

	$('#nuevaPassEd').val('');
	$('#confirmaPassEd').val('');
	$(function () {
	  $.validator.setDefaults({
	    submitHandler: function () {
	      fnActualizaMisDatos();
	    }
	  });
	  $('#frmActualizaMisDatos').validate({
	    rules: {
	      nomProfesionalEd: {
	        required: true
	      },
	      correoProfesionalEd: {
	        required: true,
	        email:true
	      },
	      fonoProfesionalEd: {
	        required: true,
	        number: true
	      }
	    },
	    messages: {
		    nomProfesionalEd: {
		      required: "Dato Obligatorio"
		    },
		    correoProfesionalEd: {
		      required: "Dato Obligatorio",
		      email: "Ingrese mail válido"
		    },
		    fonoProfesionalEd: {
		      required: "Dato Obligatorio",
		      number: "Solo números"
		    }
	    },
	    errorElement: 'span',
	    errorPlacement: function (error, element) {
	      error.addClass('invalid-feedback');
	      element.closest('.input-group').append(error);
	    },
	    highlight: function (element, errorClass, validClass) {
	      $(element).addClass('is-invalid');
	    },
	    unhighlight: function (element, errorClass, validClass) {
	      $(element).removeClass('is-invalid');
	    }
	  });
	});

	//VALIDACION CONTRASEÑA
	$(document).ready(function(){

		var mayus= new RegExp("^(?=.*[A-Z])");
		var tamanio= new RegExp("^(?=.{6,})");

		var regExp= [mayus,tamanio];
		var regExp2= [mayus,tamanio];
		var elementos= [$("#mayus"),$("#tamanio")];
		var elementos2= [$("#mayus2"),$("#tamanio2")];

		$("#nuevaPassEd").on("keyup", function(){
			var pass= $("#nuevaPassEd").val();
			var check= 0;
			// alert(pass.length);
			for(var i = 0; i < 2; i++){
				if(regExp[i].test(pass)){
					elementos[i].hide();
					check++;  
				}else{
					elementos[i].show();
				}
				if(check == 2){
					$("#spPassword").last().addClass("alert-success");
					$("#msjPass").text("Segura").css("color", "green");
					$("#nuevaPassEd").css("border","2px solid green");
				}
				if(check != 2){
					$("#msjPass").text("Insegura").css("color", "red");
					$("#nuevaPassEd").css("border","2px solid red");
				}
			}
		});

		$("#confirmaPassEd").on("keyup", function(){
			var pass1= $("#nuevaPassEd").val();
			var pass2= $("#confirmaPassEd").val();
			var check2= 0;

			//alert(pass.length);
			for(var j = 0; j < 2; j++){
				if(regExp2[j].test(pass2)){
					elementos2[j].hide();
					check2++;
				}else{
					elementos2[j].show();
				}
			}

			if(check2 == 2){
				$("#spPassword2").last().addClass("alert-success");
				$("#msjPass2").text("Segura").css("color", "green");
				$("#confirmaPassEd").css("border","2px solid green");				
			}
			if(check2 != 2){
				$("#msjPass2").text("Insegura").css("color", "red");
				$("#confirmaPassEd").css("border","2px solid red");
			}

			// if(check2 >= 0 && check2 <= 2){
			// 	//$("#msjPass2").text("Muy insegura").css("color", "red");
			// 	$("#confirmaPassEd").css("border","2px solid red");
			// }else if(check2 >= 3 && check2 <= 5){
			// 	//$("#msjPass2").text("Poco segura").css("color", "orange");
			// 	$("#confirmaPassEd").css("border","2px solid orange");
				
			// }else if(check2 == 6){
			// 	//$("#msjPass2").text("segura").css("color", "green");
			// 	//$("#msjPass").html("<p class='text-success'>SeguraXXX</p>");
			// 	$("#confirmaPassEd").css("border","2px solid green");
			// }			
		});

	});
	// FIN VALIDACION CONTRASEÑA

	var usuario= $("#hdRutMisDatosEd").val();
    var cadena='usuario=' + usuario;
  
    $.ajax({
      	type: 'POST',
      	url: 'vistas/mantenedores/misDatos/buscaMisDatos.php',
      	data: cadena,
      	dataType: 'json',
      	success: function(arr){

	        var nombre= arr.nombre;
	        var correo= arr.correo;
	        var fono= arr.fono;

	        $('#nomProfesionalEd').val(nombre);
	        $('#correoProfesionalEd').val(correo);
	        $('#fonoProfesionalEd').val(fono);
      }
    });

	function fnActualizaMisDatos(){

		nomProfesionalEd = $('#nomProfesionalEd').val();
		correoProfesionalEd = $('#correoProfesionalEd').val();
		fonoProfesionalEd = $('#fonoProfesionalEd').val();
		nuevaPassEd = $('#nuevaPassEd').val();
		confirmaPassEd = $('#confirmaPassEd').val();

		cadena = 'nomProfesionalEd=' + nomProfesionalEd +
		 		 '&correoProfesionalEd=' + correoProfesionalEd +
		 		 '&fonoProfesionalEd=' + fonoProfesionalEd +
		 		 '&nuevaPassEd=' + nuevaPassEd +
		 		 '&confirmaPassEd=' + confirmaPassEd;

		if(nuevaPassEd==confirmaPassEd){

			$.ajax({
				type:"post",
				data:cadena,
				url:'vistas/mantenedores/misDatos/guardaActualizaMisDatos.php',
				success:function(r){
					if (r == 1) {

						Swal.fire({
						  position: 'top-end',
						  icon: 'success',
						  title: 'Sus datos han sido actualizados con éxito!!',
						  showConfirmButton: false,
						  timer: 1500
						})
						$('#nomProfesionalEd').val('');
			        	$('#correoProfesionalEd').val('');
			        	$('#fonoProfesionalEd').val('');
			        	$('#nuevaPassEd').val('');
			        	$('#confirmaPassEd').val('');
			        	$("#modalMisDatos").hide();
			        	window.location.replace("principal.php");
				    } else {
				    }					
				}
			});

		}else{
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'Las contraseñas no coinciden!',
			})

			$('#nuevaPassEd').val('');
			$('#confirmaPassEd').val('');
		}

		
	}

</script>
