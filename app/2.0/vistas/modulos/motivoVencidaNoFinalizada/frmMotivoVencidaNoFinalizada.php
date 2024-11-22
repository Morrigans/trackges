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

$marca= $qrDerivacion->Fields('MARCA');

if ($marca == 'para_cierre') {
	$query_qrMotivo= "SELECT * FROM $MM_oirs_DATABASE.motivo_vencida_no_finalizada where categoria = 'para_cierre' order by MOTIVO asc";
	$qrMotivo = $oirs->SelectLimit($query_qrMotivo) or die($oirs->ErrorMsg());
	$totalRows_qrMotivo = $qrMotivo->RecordCount();
}else{
	$query_qrMotivo= "SELECT * FROM $MM_oirs_DATABASE.motivo_vencida_no_finalizada where categoria != 'para_cierre' order by MOTIVO asc";
	$qrMotivo = $oirs->SelectLimit($query_qrMotivo) or die($oirs->ErrorMsg());
	$totalRows_qrMotivo = $qrMotivo->RecordCount();
}



$hoy = date('Y-m-d');


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Motivo Pendiente Cierre:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesMotivoVencidaNoFinalizada"></div>	

						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Motivo</span>
							    </div>
							    <select name="slMotivoVencidaNoFinalizada" id="slMotivoVencidaNoFinalizada" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrMotivo->EOF) {?>
							          <option value="<?php echo $qrMotivo->Fields('ID_MOTIVO') ?>"><?php echo utf8_encode($qrMotivo->Fields('MOTIVO')) ?></option>
							        <?php $qrMotivo->MoveNext(); } ?>
							    </select>
							</div>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-success" data-dismiss="modal" onclick="fnMotivoVencidaNoFinalizada('<?php echo $idDerivacion ?>','<?php echo $tipoUsuario ?>')">Guardar motivo</button>
			  	</div>     
	  		</div>
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	idDerivacion = $('#idDerivacion').val();	
	$('#dvInfoVentanasOpcionesMotivoVencidaNoFinalizada').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

	function fnMotivoVencidaNoFinalizada(idDerivacion, tipoUsuario){
		slMotivoVencidaNoFinalizada = $('#slMotivoVencidaNoFinalizada').val();

		if (slMotivoVencidaNoFinalizada == '') {
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'Complete los datos requeridos!',
			})
		}else{
			cadena = 'idDerivacion=' + idDerivacion +
				 '&slMotivoVencidaNoFinalizada=' + slMotivoVencidaNoFinalizada ;
			$.ajax({
				type:"post",
				data:cadena,
				url:'vistas/modulos/motivoVencidaNoFinalizada/motivoVencidaNoFinalizada.php',
				success:function(r){
					if (r == 1) {
						Swal.fire({
						  position: 'top-end',
						  icon: 'success',
						  title: 'La derivacion ha sido cerrada con exito',
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

					//setTimeout(function (){ $('#contenido_principal').load('vistas/inicio/inicioSupervisora/inicioSupervisora.php'); }, 1401);
		    	}
					
				}
			});	
		}
	}

</script>



