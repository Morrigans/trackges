<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$idUsuario = $_SESSION['idUsuario'];
$tipoUsuario = $_SESSION['tipoUsuario'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
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
	        	<div class="col-md-6"><h2>Aceptar Caso paciente </h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpcionesAceptarCaso"></div>	
						<div class="col-md-6">
							<span class="label label-default">Comentario bitácora<br></span>
						<!-- si no viene reasignada -->
						    <textarea name="comentarioBitacoraAceptarCaso" id="comentarioBitacoraAceptarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo $qrPaciente->Fields('NOMBRE'); ?> rut <?php echo $codRutPac; ?> ha sido aceptada. </textarea>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnAceptarCasoPrimerPrestador('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Aceptar Caso</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="hdIdDerivacionAceptarCaso" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#hdIdDerivacionAceptarCaso').val();	
$('#dvInfoVentanasOpcionesAceptarCaso').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

function fnAceptarCasoPrimerPrestador(idDerivacion, tipoUsuario){
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
			url:'vistas/modulos/pacientesPp/aceptarCaso/aceptarCasoPp.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido aceptada con exito',
					  showConfirmButton: false,
					  timer: 800
					})
					setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1000);
	    		}
				
			}
		});
	}
} 

	
</script>



