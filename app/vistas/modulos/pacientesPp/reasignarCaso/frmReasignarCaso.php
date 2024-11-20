<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');
$idDerivacionPp = $qrDerivacion->Fields('ID_DERIVACION_PP');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia_pp WHERE CODIGO_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');

$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

$query_qrAsignarEnfermeria= "SELECT * FROM $MM_oirs_DATABASE.login WHERE TIPO = '3' order by NOMBRE asc";
$qrAsignarEnfermeria = $oirs->SelectLimit($query_qrAsignarEnfermeria) or die($oirs->ErrorMsg());
$totalRows_qrAsignarEnfermeria = $qrAsignarEnfermeria->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas_pp WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas_pp WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();


?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Reasignar Caso:</h2></div>
						<div class="col-md-6" id="dvInfoVentanasOpcionesReAsignaPp"></div>	
						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Reasignar a</span>
							    </div>
							    <select name="slAsignarEnfermeriaDerivacion" id="slAsignarEnfermeriaDerivacion" class="form-control input-sm" onchange="fnCambiaComentarioBitacora(this.value,'<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>','<?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>','<?php echo $qrPaciente->Fields('COD_RUTPAC'); ?>')">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrAsignarEnfermeria->EOF) {?>
							          <option value="<?php echo $qrAsignarEnfermeria->Fields('USUARIO') ?>"><?php echo utf8_encode($qrAsignarEnfermeria->Fields('NOMBRE')) ?></option>
							        <?php $qrAsignarEnfermeria->MoveNext(); } ?>
							    </select>
							</div>
							<span class="label label-default">Comentario bitácora<br></span>
		  				<textarea name="comentarioBitacoraReasignarCaso" id="comentarioBitacoraReasignarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?> rut <?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?> ha sido reasignada. </textarea>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="fnReasignarCaso('<?php echo $idDerivacion ?>','<?php echo $idDerivacionPp ?>')">Reasignar Caso</button>
			  	</div>     
	  		</div>

	  		<input type="hidden" id="idDerivacionReAsigna" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>

idDerivacion = $('#idDerivacionReAsigna').val();
$('#dvInfoVentanasOpcionesReAsignaPp').load('vistas/modulos/infoVentanasOpcionesPp.php?idDerivacion='+idDerivacion);

function fnReasignarCaso(idDerivacion,idDerivacionPp){
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
			 '&idDerivacionPp=' + idDerivacionPp +
			 '&comentarioBitacoraReasignarCaso=' + comentarioBitacoraReasignarCaso;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/modulos/pacientesPp/reasignarCaso/reasignarCaso.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'La derivacion ha sido reasignada con exito',
					  showConfirmButton: false,
					  timer: 1400
					})
			setTimeout(function (){ $('#contenido_principal').load('vistas/modulos/pacientesPp/frmPacientesPp.php'); }, 1401);
	    	}
				
			}
		});
	}
} 

function fnCambiaComentarioBitacora(enfermera,nderivacion,paciente,rutPaciente){
	nenfermera = $('select[name="slAsignarEnfermeriaDerivacion"] option:selected').text();
	$('#comentarioBitacoraReasignarCaso').val('La derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' ha sido reasignada a enfermera '+nenfermera +'.');
}
	
</script>



