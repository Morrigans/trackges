<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$enfermera = $qrDerivacion->Fields('ENFERMERA');

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '3' order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('FOLIO'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Caso:</h2></div>
					<div class="col-md-6" id="dvInfoVentanasOpcionesAsignarCaso"></div>
					<div class="col-md-6">
						<?php if ($enfermera == '' or $enfermera == '0') { ?>
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Asignar a</span>
							    </div>
							    <select name="slAsignarEnfermeriaDerivacion" id="slAsignarEnfermeriaDerivacion" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarEnfermeria->EOF) {?>
							          <option value="<?php echo $qrAsignarEnfermeria->Fields('USUARIO') ?>"><?php echo $qrAsignarEnfermeria->Fields('NOMBRE') ?></option>
							        <?php $qrAsignarEnfermeria->MoveNext(); } ?>
							    </select>
							</div>
						<?php }else{ ?><br>
							<h5>Este caso ya tiene Gestor asignado</h5>
						<?php } ?>
						
							
					</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="fnAsignarCaso('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Asignar Caso</button>
			  	</div>     
	  		</div>
	  		<input type="hidden" id="idDerivacionAsignarCaso" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacionAsignarCaso').val();	
$('#dvInfoVentanasOpcionesAsignarCaso').load('2.0/vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnAsignarCaso(idDerivacion, tipoUsuario){
	slAsignarEnfermeriaDerivacion = $('#slAsignarEnfermeriaDerivacion').val();
	comentarioBitacoraReasignarCaso = $('#comentarioBitacoraReasignarCaso').val(); 

	if (slAsignarEnfermeriaDerivacion == '' || comentarioBitacoraReasignarCaso == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se reasigno el caso, complete los datos requeridos!',
		})
	}else{

	cadena = 'idDerivacion=' + idDerivacion +
			 '&slAsignarEnfermeriaDerivacion=' + slAsignarEnfermeriaDerivacion +
			 '&comentarioBitacoraReasignarCaso=' + comentarioBitacoraReasignarCaso;
		$.ajax({
			type:"post",
			data:cadena,
			url:'2.0/vistas/modulos/asignarCaso/asignarCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido asignada con exito',
					  showConfirmButton: false,
					  timer: 800
					})

				if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
					setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
				}
				if (tipoUsuario == 4) {// administrativa
					setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
				}
				// if (tipoUsuario == 5) {//medico
				// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
				// }
				if (tipoUsuario == 6) {//tens
					setTimeout(function (){ $('#contenido_principal').load('2.0/vistas/inicio/inicioTens/inicioTens.php'); }, 1);
				}

	    	}
				
			}
		});
	}
} 

</script>



