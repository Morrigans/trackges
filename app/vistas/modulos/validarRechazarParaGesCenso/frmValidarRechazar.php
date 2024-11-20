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


$query_qrCenso = "SELECT * FROM $MM_oirs_DATABASE.api_censo WHERE ID_DERIVACION = '$idDerivacion'";
$qrCenso = $oirs->SelectLimit($query_qrCenso) or die($oirs->ErrorMsg());
$totalRows_qrCenso = $qrCenso->RecordCount();

 $estado = $qrCenso->Fields('ESTADO'); 
 $idCenso = $qrCenso->Fields('id_censo');

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
	        	<div class="col-md-6"><h2>Validar/Rechazar para Ges</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpcionesCenso"></div>	
						<div class="col-md-6">
						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Validar/Rechazar</span>
						    </div>
						    <select name="slAgregarEstadoCenso" id="slAgregarEstadoCenso" class="form-control input-sm">
						    	<?php if ($estado =='sin validar') { ?>
						    		<option value="">Seleccionar...</option>
						    		<option value="validado">Validar</option>
						    		<option value="rechazado">Rechazar</option>
						    	<?php } 
						    	if ($estado =='validado') { ?>
						    		<option value="rechazado">Rechazar</option>
						    	<?php }
						    	if ($estado == 'rechazado') { ?>
						    		<option value="validado">Validar</option>
						    	<?php } ?>
						    </select>
						</div>

						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Comentario</span>
						    </div>
						    <textarea class="form-control input-sm" id="textComentarioCenso" name="textComentarioCenso"></textarea>
						</div>
		  				
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success"  onclick="fnValidarRechazarCenso('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>','<?php echo $idCenso ?>',)">Actualizar</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacion').val();	
$('#dvInfoVentanasOpcionesCenso').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnValidarRechazarCenso(idDerivacion, tipoUsuario, idCenso){
	estadoCenso = $('#slAgregarEstadoCenso').val();
	comentarioCenso = $('#textComentarioCenso').val();
	estado = 'censo';
if (comentarioCenso=='' && estadoCenso=='rechazado' ) {

Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Ingrese Comentario!',

})

}else{

	$('#modalValidarRechazar').modal('hide');

cadena = 'idDerivacion=' + idDerivacion +
			'&tipoUsuario=' + tipoUsuario+
			'&idCenso=' + idCenso+
			'&estadoCenso=' + estadoCenso+
			'&comentarioCenso=' + comentarioCenso;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/validarRechazarParaGesCenso/agregarEstadoCenso.php',
			success:function(r){

				if (r == 1) {
		

					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'El Ingreso hospitalario ha sido validado',
					  showConfirmButton: false,
					  timer: 2000
					})
					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001); 
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001);
					}
	    		}else{

	    			Swal.fire({
	    			  position: 'top-end',
	    			  icon: 'success',
	    			  title: 'El Ingreso hospitalario ha sido rechazado',
	    			  showConfirmButton: false,
	    			  timer: 2000
	    			})

	    			if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001); 
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/tablaDerivacionesApiCenso.php?estado=' + estado); }, 2001);
					}
	    		}
				
			}
		});



		}
	}
	
</script>



