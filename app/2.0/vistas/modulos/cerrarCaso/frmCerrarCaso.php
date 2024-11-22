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

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA = '$codPatologia'";
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

$query_qrMotivoCierre= "SELECT * FROM $MM_oirs_DATABASE.motivo_cierre_caso order by DESC_CIERRE_CASO asc";
$qrMotivoCierre = $oirs->SelectLimit($query_qrMotivoCierre) or die($oirs->ErrorMsg());
$totalRows_qrMotivoCierre = $qrMotivoCierre->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$hoy = date('Y-m-d');
$query_qrCanasta = "SELECT * 
	FROM 
		$MM_oirs_DATABASE.derivaciones, 
		$MM_oirs_DATABASE.derivaciones_canastas
	WHERE 
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION = '$idDerivacion' AND
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION = $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION AND
		$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' AND 
		$MM_oirs_DATABASE.derivaciones_canastas.ESTADO='activa' AND
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE > '$hoy' AND
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE != '0000-00-00'";
$qrCanasta = $oirs->SelectLimit($query_qrCanasta) or die($oirs->ErrorMsg()); 
$trs_qrCanasta = $qrCanasta->RecordCount();




//busca canastas activas vencidas para no dejar cerrar caso sin finalizar estas canastas
$query_qrCanastaVencida = "SELECT * 
	FROM 
		$MM_oirs_DATABASE.derivaciones, 
		$MM_oirs_DATABASE.derivaciones_canastas
	WHERE 
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION = '$idDerivacion' AND
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION = $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION AND
		$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' AND 
		$MM_oirs_DATABASE.derivaciones_canastas.ESTADO='activa' AND 
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE != '0000-00-00' AND
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE < '$hoy'";
$qrCanastaVencida = $oirs->SelectLimit($query_qrCanastaVencida) or die($oirs->ErrorMsg());
$trs_qrCanastaVencida = $qrCanastaVencida->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
	        	<div class="col-md-6"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></div>
	        	<div class="col-md-6"><h2>Cerrar Caso:</h2></div>

						<div class="col-md-6" id="dvInfoVentanasOpcionesCerrarCaso"></div>	

						<div class="col-md-6">
							<div class="input-group mb-3 col-sm-12">
							    <div class="input-group-prepend">
							      <span class="input-group-text">Motivo</span>
							    </div>
							    <select name="slMotivoCierreCaso" id="slMotivoCierreCaso" class="form-control input-sm" onchange="fnCambiaComentarioBitacora(this.value,'<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>','<?php echo $qrPaciente->Fields('NOMBRE'); ?>','<?php echo $qrPaciente->Fields('COD_RUTPAC'); ?>')">
							        <option value="">Seleccione...</option>
							        <?php while (!$qrMotivoCierre->EOF) {?>
							          <option value="<?php echo $qrMotivoCierre->Fields('ID_CIERRE_CASO') ?>"><?php echo $qrMotivoCierre->Fields('DESC_CIERRE_CASO') ?></option>
							        <?php $qrMotivoCierre->MoveNext(); } ?>
							    </select>
							</div>
							<span class="label label-default">Comentario bitácora<br></span>
		  					<textarea name="comentarioBitacoraCerrarCaso" id="comentarioBitacoraCerrarCaso" cols="11" rows="10" class="form-control input-sm">La derivación número <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?> del paciente <?php echo $qrPaciente->Fields('NOMBRE'); ?> rut <?php echo $qrDerivacion->Fields('COD_RUTPAC'); ?> ha sido cerrada. </textarea>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
					    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="fnCerrarCaso('<?php echo $idDerivacion ?>', '<?php echo $trs_qrCanasta ?>', '<?php echo $trs_qrCanastaVencida ?>','<?php echo $tipoUsuario ?>')">Cerrar Caso</button>
			  	</div>     
	  		</div>
	  		<input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	idDerivacion = $('#idDerivacion').val();	
	$('#dvInfoVentanasOpcionesCerrarCaso').load('vistas/modulos/infoVentanasOpciones.php?idDerivacion='+idDerivacion);

	function fnCerrarCaso(idDerivacion, trsCanasta, trsCanastaVencida, tipoUsuario){
		comentarioBitacoraCerrarCaso = $('#comentarioBitacoraCerrarCaso').val();
		slMotivoCierreCaso = $('#slMotivoCierreCaso').val();

		if (slMotivoCierreCaso == '' || comentarioBitacoraCerrarCaso == '') {
			Swal.fire({
			  icon: 'error',
			  title: 'Oops...',
			  text: 'No se cerro el caso, complete los datos requeridos!',
			})
		}else{

				cadena = 'idDerivacion=' + idDerivacion +
					 '&slMotivoCierreCaso=' + slMotivoCierreCaso +
					 '&comentarioBitacoraCerrarCaso=' + comentarioBitacoraCerrarCaso;
				$.ajax({
					type:"post",
					data:cadena,
					url:'vistas/modulos/cerrarCaso/cerrarCaso.php',
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

	function fnCambiaComentarioBitacora(enfermera,nderivacion,paciente,rutPaciente){
		motivo = $('select[name="slMotivoCierreCaso"] option:selected').text();
		$('#comentarioBitacoraCerrarCaso').val('La derivación número '+ nderivacion + ' del paciente '+paciente + ' rut '+rutPaciente + ' ha sido cerrada por el siguiente motivo: '+motivo +'.');
	}
	
</script>



