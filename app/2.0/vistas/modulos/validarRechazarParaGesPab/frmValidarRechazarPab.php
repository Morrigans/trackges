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


$query_qrPab = "SELECT * FROM $MM_oirs_DATABASE.api_pabellones WHERE ID_DERIVACION = '$idDerivacion'";
$qrPab = $oirs->SelectLimit($query_qrPab) or die($oirs->ErrorMsg());
$totalRows_qrPab = $qrPab->RecordCount();

 $estado = $qrPab->Fields('ESTADO_VALIDACION'); 
 $idPab = $qrPab->Fields('id_pabellones');

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
						<div class="col-md-6" id="dvInfoVentanasOpcionesPab"></div>	
						<div class="col-md-6">
						<div class="input-group mb-3 col-sm-12">
						    <div class="input-group-prepend">
						      <span class="input-group-text">Validar/Rechazar</span>
						    </div>
						    <select name="slAgregarEstadoPab" id="slAgregarEstadoPab" class="form-control input-sm">
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
						    <textarea class="form-control input-sm" id="textComentarioPab" name="textComentarioPab"></textarea>
						</div>
		  				
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success"  onclick="fnValidarRechazarPab('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>','<?php echo $idPab ?>',)">Actualizar</button>
			  	</div>     
	  		</div>
		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
idDerivacion = $('#idDerivacion').val();	
$('#dvInfoVentanasOpcionesPab').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

function fnValidarRechazarPab(idDerivacion, tipoUsuario, idPab){
	estadoPab = $('#slAgregarEstadoPab').val();
	comentarioPab = $('#textComentarioPab').val();
	estado = 'pabellon';
if (comentarioPab=='' && estadoPab=='rechazado' ) {

Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Ingrese Comentario!',

})

}else{

	$('#modalValidarRechazarPab').modal('hide')

cadena = 'idDerivacion=' + idDerivacion +
			'&tipoUsuario=' + tipoUsuario+
			'&idPab=' + idPab+
			'&estadoPab=' + estadoPab+
			'&comentarioPab=' + comentarioPab;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/validarRechazarParaGesPab/agregarEstadoPab.php',
			success:function(r){

				if (r == 1) {
		

					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'El Ingreso a pabellon ha sido validado',
					  showConfirmButton: false,
					  timer: 2000
					})
					if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001); 
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001);
					}
	    		}else{

	    			Swal.fire({
	    			  position: 'top-end',
	    			  icon: 'success',
	    			  title: 'El Ingreso a pabellon ha sido rechazado',
	    			  showConfirmButton: false,
	    			  timer: 2000
	    			})

	    			if (tipoUsuario == 1 || tipoUsuario == 2 || tipoUsuario == 3) {
						setTimeout(function (){ $('#dvTablaDerivaciones').load('vistas/inicio/inicioSupervisora/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001); 
					}
					if (tipoUsuario == 4) {// administrativa
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioAdministrativa/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001);
					}
					// if (tipoUsuario == 5) {//medico
					// 	setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1);
					// }
					if (tipoUsuario == 6) {//tens
						setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioTens/tablaDerivacionesApiPabellon.php?estado=' + estado); }, 2001);
					}
	    		}
				
			}
		});



		}
	}
	
</script>



