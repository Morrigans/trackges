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

 $idPrgPab = $_REQUEST['idPrgPab'];

$query_qrPrgPab = "SELECT * FROM $MM_oirs_DATABASE.api_prog_pabellones WHERE ID = '$idPrgPab'";
$qrPrgPab = $oirs->SelectLimit($query_qrPrgPab) or die($oirs->ErrorMsg());
$totalRows_qrPrgPab = $qrPrgPab->RecordCount();

 $idDerivacion = $qrPrgPab->Fields('ID_DERIVACION'); 

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
	        	<div class="col-md-6"><h2>Motivo retraso pabellón programado</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpcionesMotRetPabPrg"></div>	
						<div class="col-md-6">
						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Motivo</span>
						    </div>
						    <select name="slMotivo" id="slMotivo" class="form-control input-sm">
						    		<option value="">Seleccionar...</option>
						    		<option value="Causa paciente">Causa paciente</option>
						    		<option value="Causa de la clinica">Causa de la clínica</option>
						    </select>
						</div>

<!-- 						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Comentario</span>
						    </div>
						    <textarea class="form-control input-sm" id="textComentarioCenso" name="textComentarioCenso"></textarea>
						</div> -->
		  				
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success"  onclick="fnMotivo('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>','<?php echo $idPrgPab ?>',)">Actualizar</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="idDerivacionMotRetPabPrg" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacionMotRetPabPrg').val();	
$('#dvInfoVentanasOpcionesMotRetPabPrg').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnMotivo(idDerivacion, tipoUsuario, idPrgPab){
	slMotivo = $('#slMotivo').val();
	// comentarioCenso = $('#textComentarioCenso').val();
	estado = 'prgPab';
if (slMotivo=='') {

Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Seleccione Motivo!',

})

}else{

	$('#modalMotRetPabPrg').modal('hide');

cadena = 'idDerivacion=' + idDerivacion +
			'&tipoUsuario=' + tipoUsuario +
			'&idPrgPab=' + idPrgPab +
			'&slMotivo=' + slMotivo;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/motRetPabPrg/agregarMotRetPabPrg.php',
			success:function(r){

				if (r == 1) {
		

					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'El Ingreso del motivo fue exitoso!',
					  showConfirmButton: false,
					  timer: 2000
					})
					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiPrgPabellones.php?estado=' + estado); }, 2001); 
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/tablaDerivacionesApiPrgPabellones.php?estado=' + estado); }, 2001);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/tablaDerivacionesApiPrgPabellones.php?estado=' + estado); }, 2001);
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



