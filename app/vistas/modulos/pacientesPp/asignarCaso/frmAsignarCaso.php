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

$query_qrDerivacionPp = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionPp = $oirs->SelectLimit($query_qrDerivacionPp) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionPp = $qrDerivacionPp->RecordCount();

$enfermera = $qrDerivacionPp->Fields('ENFERMERA');
$idDerivacionPp = $qrDerivacionPp->Fields('ID_DERIVACION_PP');

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '3' order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacionPp->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Asignar Caso:</h2></div>
					<div class="col-md-6" id="dvInfoVentanasOpcionesAsigna"></div>
					<div class="col-md-6">
						<?php if ($enfermera == '' or $enfermera == '0') { ?>
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Asignar a</span>
							    </div>
							    <select name="slAsignarEnfermeriaDerivacion" id="slAsignarEnfermeriaDerivacion" class="form-control input-sm">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarEnfermeria->EOF) {?>
							          <option value="<?php echo $qrAsignarEnfermeria->Fields('ID') ?>"><?php echo $qrAsignarEnfermeria->Fields('NOMBRE') ?></option>
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
					    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="fnAsignarCasoPp('<?php echo $idDerivacion ?>','<?php echo $idDerivacionPp ?>','<?php echo $tipoUsuario ?>')">Asignar Caso</button>
			  	</div>     
	  		</div>
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>

idDerivacion = $('#idDerivacion').val();
$('#dvInfoVentanasOpcionesAsigna').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

function fnAsignarCasoPp(idDerivacion,idDerivacionPp, tipoUsuario){
	slAsignarEnfermeriaDerivacion = $('#slAsignarEnfermeriaDerivacion').val();
	comentarioBitacoraReasignarCaso = $('#comentarioBitacoraReasignarCaso').val(); 

	if (slAsignarEnfermeriaDerivacion == '' || comentarioBitacoraReasignarCaso == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se asigno el caso, complete los datos requeridos!',
		})
	}else{

	cadena = 'idDerivacion=' + idDerivacion +
			 '&idDerivacionPp=' + idDerivacionPp +
			 '&slAsignarEnfermeriaDerivacion=' + slAsignarEnfermeriaDerivacion +
			 '&comentarioBitacoraReasignarCaso=' + comentarioBitacoraReasignarCaso;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/pacientesPp/asignarCaso/asignarCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido asignada con exito',
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



