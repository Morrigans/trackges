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

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Aceptar Caso</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpciones"></div>	
						<div class="col-md-6">
							<span class="label label-default">Comentario bitácora<br></span>
						<!-- si no viene reasignada -->
						<?php if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'no') { ?>
						    <textarea name="comentarioBitacoraAceptarCaso" id="comentarioBitacoraAceptarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo $qrPaciente->Fields('NOMBRE'); ?> rut <?php echo $codRutPac; ?> ha sido aceptada. </textarea>
						<?php } ?>

						<!-- si viene reasignada sin prestador asignado -->
						<?php if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') == '') { ?>
						   <textarea name="comentarioBitacoraAceptarCaso" id="comentarioBitacoraAceptarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo $qrPaciente->Fields('NOMBRE'); ?> rut <?php echo $codRutPac; ?> ha sido aceptada. </textarea>
						<?php } ?>

						<!-- si viene reasignada con prestador asignado -->
						<?php if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') != '') { ?>
						    <textarea name="comentarioBitacoraAceptarCaso" id="comentarioBitacoraAceptarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo $qrPaciente->Fields('NOMBRE'); ?> rut <?php echo $codRutPac; ?> ha sido aceptada y queda en estado "prestador" debido a que viene Reasignada con prestador definido por Gestora anterior. </textarea>
						<?php } ?>
		  				
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnAceptarCaso('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Aceptar Caso</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacion').val();	
$('#dvInfoVentanasOpciones').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnAceptarCaso(idDerivacion, tipoUsuario){
	comentarioBitacoraAceptarCaso = $('#comentarioBitacoraAceptarCaso').val();

	if (comentarioBitacoraAceptarCaso == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se acepto el caso, debe dejar registro en bitacora!',
		})
	}else{
	cadena = 'idDerivacion=' + idDerivacion +
					 '&comentarioBitacoraAceptarCaso=' + comentarioBitacoraAceptarCaso;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/aceptarCaso/aceptarCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido aceptada con exito',
					  showConfirmButton: false,
					  timer: 800
					})
					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/inicioSAdministrativa.php'); }, 1);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/inicioTens.php'); }, 1);
					}
	    		}
				
			}
		});
	}
} 

	
</script>



