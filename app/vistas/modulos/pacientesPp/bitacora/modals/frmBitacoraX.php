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

$usuario = $_SESSION['dni'];

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_DERIVACION = '$idDerivacion'";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$sesion = $qrBitacora->Fields('SESION');

$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
$totalRows_qrLogin = $qrLogin->RecordCount();

$tipoUsuario = $qrLogin->Fields('TIPO');

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

$idPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA = '$idPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();


?>
<style type="text/css">
	.anyClass {
	  height:600px;
	  overflow-y: scroll;
	}
</style>
<!DOCTYPE html>
<html>
	<body>
		    
        	<div class="row">
  					<div class="col-md-5">
  						<div class="input-group mb-3">
  						    <div class="input-group-prepend">
  						      <span class="input-group-text">Tipo Registro</span>
  						    </div>
  						    <?php if ($tipoUsuario == '1') { // Administrador?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	    <option value="Llamada">Llamada</option>
  						    	</select>
  						    <?php } ?>

  						    <?php if ($tipoUsuario == '2') { // supervisora?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	</select>
  						    <?php } ?>

  						    <?php if ($tipoUsuario == '3') { // gestora?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	    <option value="Llamada">Llamada</option>
  						    	</select>
  						    <?php } ?>

  						    <?php if ($tipoUsuario == '4') { // administrativa?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	    <option value="Llamada">Llamada</option>
  						    	</select>
  						    <?php } ?>

  						    <?php if ($tipoUsuario == '5') { // medico?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	    <option value="Llamada">Llamada</option>
  						    	</select>
  						    <?php } ?>

  						    <?php if ($tipoUsuario == '6') { // tens?>
  						    	<select name="slTipoRegistroBitacora" id="slTipoRegistroBitacora" class="form-control input-sm">
  						    	    <option value="">Seleccione...</option>
  						    	    <option value="Bono">Bono</option>
  						    	    <option value="Información">información</option>
  						    	    <option value="Solicita gestión">Solicita gestión</option>
  						    	    <option value="Solicita Información">Solicita información</option>
  						    	    <option value="Llamada">Llamada</option>
  						    	</select>
  						    <?php } ?>
  						    
  						</div>
  						<span class="label label-default">Comentario bitácora<br></span>
  	  					<textarea name="comentarioBitacora" id="comentarioBitacora" rows="5" class="form-control input-sm" placeholder="Ingrese un comentario a bitácora"></textarea>

			       		<div class="modal-footer" align="right">	
						    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
						    <button type="button" class="btn btn-success btn-sm" onclick="fnAgregarBitacora(<?php echo $idDerivacion ?>)">Agregar a Bitacora</button>
						    <!-- <a href="vistas/bitacora/modals/modalAudios.php?a=<?php echo $totalRows_qrDerivacionCanasta; ?>" data-toggle="modal" data-target="#modalAudios"><span class="badge badge-danger"><i class="fas fa-microphone"></i></span></a> --><br>
						    <!-- <a href="vistas/bitacora/audio/audio.php" target="_blank"><span class="badge badge-danger"><i class="fas fa-microphone"></i></span></a><br> -->
						    <div id="dvFrmGrabarAudio"></div>
				  		</div><br>
				  		<p class="lead"><h2>[<?php echo $qrDerivacion->Fields('N_DERIVACION'); ?>]</h2></p>
				  		<div class="table-responsive">
				  			<table class="table">
				  				<tr>
				  					<th style="width:50%">Paciente:</th>
				  					<td><?php 
											$codRutPac = explode(".", $codRutPac);
											$rut0 = $codRutPac[0]; // porción1
											$rut1 = $codRutPac[1]; // porción2
											$rut2 = $codRutPac[2]; // porción2
											$codRutPac = $rut0.$rut1.$rut2;
											echo $codRutPac;
										?> <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?>
									</td>
				  				</tr>
				  				<tr>
				  					<th style="">Convenio:</th>
				  					<td><?php echo utf8_encode($qrConvenio->Fields('PREVISION')); ?></td>
				  				</tr>
				  				<tr>
				  					<th style="">Fecha Derivación:</th>
				  					<td><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></td>
				  				</tr>
				  				<tr>
				  					<th>Tipo patología:</th>
				  					<td><?php echo utf8_encode($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA')); ?></td>
				  				</tr>
				  				<tr>
				  					<th>Patología:</th>
				  					<td><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></td>
				  				</tr>
				  				<tr>
				  					<th>Etapa patología:</th>
				  					<td>
											<?php 
												if ($totalRows_qrDerivacionCanasta == 0) {
													echo 'No hay etapas activas';
												}else{
													$i=1;
													while (!$qrDerivacionEtapa->EOF) {
														$codEtapaPatologia = $qrDerivacionEtapa->Fields('CODIGO_ETAPA_PATOLOGIA');
														$idEtapaPatologia = $qrDerivacionEtapa->Fields('ID_ETAPA_PATOLOGIA');

														$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
														$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
														$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

														

														$query_qrBuscaCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACIONES_ETAPA = '$idEtapaPatologia' AND ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
														$qrBuscaCanastaPatologia = $oirs->SelectLimit($query_qrBuscaCanastaPatologia) or die($oirs->ErrorMsg());
														$totalRows_qrBuscaCanastaPatologia = $qrBuscaCanastaPatologia->RecordCount();

														if ($totalRows_qrBuscaCanastaPatologia == 0) {
															// code...
														}else{
															echo $i.'.- '.utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')).'.</br>';
															$i++;
														}

														

														
													$qrDerivacionEtapa->MoveNext();
													}
												}	
											?>
										</td>
									</tr>
									<tr>
										<th>Canasta patología:</th>
										<td>
											<?php 
												if ($totalRows_qrDerivacionCanasta == 0) {
													echo 'No hay canastas activas';
												}else{
													$i=1;
													while (!$qrDerivacionCanasta->EOF) {
														$codCanastaPatologia = $qrDerivacionCanasta->Fields('CODIGO_CANASTA_PATOLOGIA');

														$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
														$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
														$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

														echo $i.'.- '.utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA')).'.</br>';

														$i++;
													$qrDerivacionCanasta->MoveNext();
													}
												}
											?>
										</td>
				  				</tr>
				  			</table>
				  		</div>				  		
  					</div>

  					<div class="col-md-7 anyClass">
  						
  						<div id="dvTablaBitacora"></div>
  					</div>
	       	</div>
	       	
	  		</div>
      <input type="hidden" id="idDerivacion" value="<?php echo $idDerivacion ?>">
	</body>
</html>

<script>
	function fnFrmGrabarAudios(){
		a='a';
		// window.location.href='vistas/bitacora/audio/audio.php?a='+a;
		//$('#dvFrmGrabarAudio').load('vistas/bitacora/audio/audio.php');
		  // cadena = '';
		  // $.ajax({
				// type: "POST",
			 //    url: "vistas/bitacora/audio/audio.php",
			 //    data: cadena,
			 //    success:function(r){
		  //      	$('#dvAdjuntarDocumento').html(r);	
		  //     }
		  // });
	}	

function fnfrmAsignarPrestadorCaso(idDerivacion){
	$('#dvfrmAsignarPrestadorCaso').load('vistas/pacientesAceptados/modals/asignarPrestadorCaso/frmAsignarPrestadorCaso.php?idDerivacion=' + idDerivacion);
}

idDerivacion = $('#idDerivacion').val();
//$('#loading').fadeIn(5000).html('<img src="images/loading.gif"/>');

setTimeout(function (){ $('#dvTablaBitacora').load('vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion); }, 100);//retardo un milisegundo (1000 es un segundo) para evitar problema de tabla cortada

function fnAgregarBitacora(idDerivacion){
	comentarioBitacora = $('#comentarioBitacora').val();
	slTipoRegistroBitacora = $('#slTipoRegistroBitacora').val();

	if (comentarioBitacora == '' || slTipoRegistroBitacora == '') {
		Swal.fire({
		  icon: 'error',
		  title: 'Oops...',
		  text: 'No se guardo el comentario, complete los datos requeridos!',
		})
	}else{

	cadena = 'idDerivacion=' + idDerivacion +
			 '&comentarioBitacora=' + comentarioBitacora +
			 '&slTipoRegistroBitacora=' + slTipoRegistroBitacora;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacora/modals/guardaBitacora.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'El comentario se agrego con exito',
					  showConfirmButton: false,
					  timer: 2500
					})
				$('#dvTablaBitacora').load('vistas/bitacora/modals/tablaBitacora.php?idDerivacion=' + idDerivacion);
				$('#comentarioBitacora').val('');
	    	}
				
			}
		});
	}
} 

</script>




