<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];

$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$sesion'";
$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
$totalRows_qrLogin = $qrLogin->RecordCount();


?>
<style type="text/css">
	.anyClass {
	  height:900px;
	  overflow-y: scroll;
	}
</style>
<!DOCTYPE html>
<html>
	<body>
        	<div class="row">
				<div class="col-md-4">
					<h3 align="center">Bitacora Administrativa</h3><br>
					<div class="input-group mb-3">
					    <div class="input-group-prepend">
					      <span class="input-group-text">Tipo Registro</span>
					    </div>
					    <select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
					        <option value="">Seleccione...</option>
					        <option value="Información">información</option>
					        <option value="Solicita gestión">Solicita gestión</option>
					        <option value="Solicita Información">Solicita información</option>
					    </select>
					</div>
					<span class="label label-default">Comentario bitácora<br></span>
  					<textarea name="comentarioBitacora" id="comentarioBitacora" rows="5" class="form-control input-sm" placeholder="Ingrese un comentario a bitácora"></textarea>

		       		<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success btn-sm" onclick="fnAgregarBitacora()">Agregar a Bitacora</button>
			  		</div><br>

			  		<div class="card">
				  		<div class="card-header">
					  		<h3 class="card-title">Ayuda</h3>
					  		<div class="card-tools">
						  		<button type="button" class="btn btn-tool" data-card-widget="collapse">
						  		<i class="fas fa-minus"></i>
						  		</button>
						  		<button type="button" class="btn btn-tool" data-card-widget="remove">
						  		<i class="fas fa-times"></i>
						  		</button>
					  		</div>
				  		</div>
				  		
				  		<div class="card-body p-3 col-md-12"><br>
				  		<small>
				  			<ul class="users-list clearfix">
				  			<span class="badge badge-info"><i class="far fa-clock"></i> Programar</span> <em>Programe una notificación de alarma en su aplicación.</em>
				  			</ul>
				  			<ul class="users-list clearfix">
				  			<span class="badge badge-warning"><i class="fas fa-paperclip"></i> Adjuntar</span> <em>Adjunte documentos a sus registros de bitacora.</em>
				  			</ul>
				  			<ul class="users-list clearfix">
				  			<span class="badge badge-info"><i class="fas fa-share-alt"></i> Compartir</span> <em>Comparta el registro con un Prestador, Supervisora o Gestora.</em>
				  			</ul>
				  		</small>	
				  		</div>

				  		<div class="card-footer text-center">
				  		
				  		</div>

			  		</div>


		

			  		
	       		</div>
	       		<div class="col-md-8 anyClass">
	       			<div id="dvTablaBitacoraAdministrativa"></div>
	       		</div>
	  		</div>
      <input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>

setTimeout(function (){ $('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php'); }, 100);//retardo un milisegundo (1000 es un segundo) para evitar problema de tabla cortada

function fnAgregarBitacora(){
	comentarioBitacora = $('#comentarioBitacora').val();
	slTipoRegistroBitacora = $('#slTipoRegistroBitacora').val();

	if (comentarioBitacora == '' || slTipoRegistroBitacora == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se guardo el comentario, complete los datos requeridos!',
		})
	}else{

	cadena = 'comentarioBitacora=' + comentarioBitacora +
			 '&slTipoRegistroBitacora=' + slTipoRegistroBitacora;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacoraAdministrativa/modals/guardaBitacora.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'El comentario se agrego con exito',
					  showConfirmButton: false,
					  timer: 2500
					})
				$('#dvTablaBitacoraAdministrativa').load('vistas/bitacoraAdministrativa/modals/tablaBitacora.php');
				$('#comentarioBitacora').val('');
	    	}
				
			}
		});
	}
} 


</script>




