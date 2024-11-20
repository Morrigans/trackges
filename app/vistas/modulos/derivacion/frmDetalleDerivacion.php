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

$query_qrPerfil = "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrPerfil = $oirs->SelectLimit($query_qrPerfil) or die($oirs->ErrorMsg());
$totalRows_qrPerfil = $qrPerfil->RecordCount();

$tipoPerfil = $qrPerfil->Fields('TIPO');

$idDerivacion = $_REQUEST['idDerivacion'];

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_DERIVACION = '$idDerivacion'";
$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
$totalRows_qrBitacora = $qrBitacora->RecordCount();

$idBitacora = $qrBitacora->Fields('ID_BITACORA');
$ruta = $qrBitacora->Fields('RUTA_DOCUMENTO');

$query_qrDerivacionEtapa = "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

$idPaciente = $qrDerivacion->Fields('ID_PACIENTE');

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE ID = '$idPaciente'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$idPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE ID_PATOLOGIA = '$idPatologia'";
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

$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

?>

<!DOCTYPE html>
<html>
	<body>
        <div class="card-body">
        	<div class="row">
						<div class="col-md-12">
					  		<div class="table-responsive">
					  			<table id="tDatosPac" class="table">
					  				<thead>
						  				<tr class="table-secondary">
						  					<th style="width: 10%;">Derivación</th>
						  					<th style="width: 20%;">Paciente</th>
						  					<th>Convenio</th>
						  					<!-- <th>Fecha Derivación</th> -->
						  					<th>Tipo patología</th>
						  					<th>Patología</th>
						  				</tr>
						  			</thead>
						  			<tbody>
						  				<tr>
						  					<td><strong><?php echo $qrDerivacion->Fields('N_DERIVACION'); ?></strong></td>
						  					<td><?php echo $qrPaciente->Fields('COD_RUTPAC'); ?><br> <?php echo utf8_encode($qrPaciente->Fields('NOMBRE')); ?></td>
						  					<td><?php echo $qrConvenio->Fields('PREVISION'); ?></td>
											<!-- <td><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></td> -->
											<td><?php echo utf8_encode($qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA')); ?></td>
											<td><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>	
						<div class="col-md-12">
					  		<div class="table-responsive">
					  			<table class="table">
					  				<tr>
					  					<th style="text-align: center;"><a class="btn btn-default" href="#"  onclick="fnfrmAgregarEtapa('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')"><i class="fas fa-plus-square"></i> Agregar Etapas</a></th>
					  					<th>
					  						<div id="dvfrmAgregarEtapaoCanasta">
      										<div id="dvAdjuntarDocumento2"></div>
					  						<h6 align="center" class="text-muted">Para agregar etapas o canastas use las opciones disponibles</h6></div>
					  					</th>
					  				</tr>
					  				<?php
					  				while (!$qrDerivacionEtapa->EOF) { 
					  					$idEtapa = $qrDerivacionEtapa->Fields('ID_ETAPA_PATOLOGIA');
					  					$codEtapa = $qrDerivacionEtapa->Fields('CODIGO_ETAPA_PATOLOGIA');
					  					$fechaLimite = $qrDerivacionEtapa->Fields('FECHA_LIMITE');


					  					$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapa'";
					  					$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
					  					$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();
					  				?>
					  				<tr>
					  					<td style="width:20%;vertical-align:middle;text-align: center;">
					  						<div class="btn-group">
											<button type="button" class="btn btn-default"><i class="fas fa-cog"></i> <?php echo utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')); ?></button>
											<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
						  						<div class="dropdown-menu" role="menu">
													<a class="dropdown-item" href="#" onclick="fnfrmAgregarCanasta('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>','<?php echo $codEtapa ?>','<?php echo $idEtapa ?>')">Agregar Canasta</a>
												</div>
											</div>
					  					</td>
					  					<!-- <td style="width:8%;vertical-align:middle;"><strong><span class="badge badge-info"><?php echo utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')); ?></span></strong></td> -->
					  					<td>
					  						<?php 

					  						$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACIONES_ETAPA = '$idEtapa' AND ID_DERIVACION = '$idDerivacion' order by ID_CANASTA_PATOLOGIA ASC";
					  						$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
					  						$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

					  						$i = 1; ?>
					  						<div class="table-responsive">
					  						<table class="table table-hover">
					  							<tr class="table-info">
					  								<th>Adjunto</th>
					  								<th>Canasta</th>					  								
					  								<th>Médico Tratante</th>
					  								<th style="text-align: center;">Fecha Canasta</th>
					  								<th style="text-align: center;">Fecha Limite</th>
					  								<th style="text-align: center;">Dias restantes</th>
					  								<th style="text-align: center;">Opción</th>

					  							</tr>
						  						<?php	
						  						while (!$qrCanastaPatologia->EOF) { 
						  							$idDerivacionCanasta = $qrCanastaPatologia->Fields('ID_CANASTA_PATOLOGIA');
						  							$codCanasta = $qrCanastaPatologia->Fields('CODIGO_CANASTA_PATOLOGIA');

						  							$query_qrBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_CANASTA_PATOLOGIA = '$idDerivacionCanasta' AND ID_DERIVACION='$idDerivacion'";
						  							$qrBitacora = $oirs->SelectLimit($query_qrBitacora) or die($oirs->ErrorMsg());
						  							$totalRows_qrBitacora = $qrBitacora->RecordCount();

						  							$idBitacora = $qrBitacora->Fields('ID_BITACORA');
						  							$ruta = $qrBitacora->Fields('RUTA_DOCUMENTO');

					  								$query_qrCanastaDescPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanasta'";
					  								$qrCanastaDescPatologia = $oirs->SelectLimit($query_qrCanastaDescPatologia) or die($oirs->ErrorMsg());
					  								$totalRows_qrCanastaDescPatologia = $qrCanastaDescPatologia->RecordCount();

					  								$diasLimite = $qrCanastaDescPatologia->Fields('TIEMPO_LIMITE');
					  								$fechaLimite = date("Y-m-d",strtotime($qrCanastaPatologia->Fields('FECHA_CANASTA')."+ $diasLimite days"));

						  							$rutPrestador = $qrCanastaPatologia->Fields('RUT_PRESTADOR');

					  								$query_qrDescPrestadores = "SELECT * FROM $MM_oirs_DATABASE.login WHERE ID='$rutPrestador'";
										        	$qrDescPrestadores = $oirs->SelectLimit($query_qrDescPrestadores) or die($oirs->ErrorMsg());
										        	$totalRows_qrDescPrestadores = $qrDescPrestadores->RecordCount();

										        	$canasta = utf8_encode($qrCanastaDescPatologia->Fields('DESC_CANASTA_PATOLOGIA'));
					  							?>
			  										<tr> 
			  											<td>
			  												<?php if($ruta==''){ 
 
			  														if($idBitacora!=''){ ?>
  
			  															<a href="#" onclick="$('#dvfrmAgregarEtapaoCanasta').load('vistas/bitacora/adjuntaDoc/adjuntaDocumento.php?idBitacora='+<?php echo $idBitacora?>+'&idDerivacion='+<?php echo $idDerivacion?>+'&idDerivacionCanasta='+<?php echo $idDerivacionCanasta?>)"><span class="badge badge-warning"><i class="fas fa-paperclip"></i></span></a>
	  															<?php }else{} ?> 
							      							
							      						<?php }else { ?>
															<span><a target="_blank" class="btn btn-xs btn-success" href="vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>"><i class="far fa-file-pdf" ></i></a></span>

															<button class="btn btn-xs btn-danger" onclick="fnSiNoEliminaAdjunto('<?php echo $idDerivacion ?>','<?php echo $idBitacora ?>','<?php echo $ruta ?>')"><i class="far fa-file-excel"></i></button>
							      						<?php } ?>
			  											</td>
			  											<td width="40%"><?php 
			  											echo $canasta.'</br>';
			  											 ?>
			  											</td>
			  											<td>
		  													<?php echo utf8_encode($qrDescPrestadores->Fields('NOMBRE')) ?>
		  													<!-- <a href="#" onclick="fnAgregarPrestadorCanasta('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>','<?php echo $codEtapa ?>','<?php echo $idEtapa ?>','<?php echo $idDerivacionCanasta ?>')"><i class="far fa-edit"></i></a> -->
		  												</td>
			  											<td style="text-align: center;"><?php echo date("d-m-Y",strtotime($qrCanastaPatologia->Fields('FECHA_CANASTA'))); ?></td>
			  											<td style="text-align: center;"><?php 
			  													if ($qrCanastaPatologia->Fields('FECHA_LIMITE') == '' or $qrCanastaPatologia->Fields('FECHA_LIMITE') == null or $qrCanastaPatologia->Fields('FECHA_LIMITE') == 0) {?>  
			  														Sin Limite
			  													<?php }else{
			  														echo date("d-m-Y",strtotime($qrCanastaPatologia->Fields('FECHA_LIMITE'))); 
			  													 }  
			  												?>
			  											</td>
			  											<td width="20%" style="text-align: center;">
			  												<?php //calculo de dias restantes
				  												date_default_timezone_set('America/Santiago');
																$hoy= date('Y-m-d');
			  													$date1 = new DateTime($hoy);
			  													$date2 = new DateTime($fechaLimite);
			  													$diff = $date1->diff($date2);
			  													// will output 2 days
			  													
			  													 if ($qrCanastaPatologia->Fields('ESTADO') == 'finalizada') {?>
			  													 	<span class="badge badge-success"><font size="2">Finalizada</font></span>
			  													<?php }else{
				 	  													 	if ($diasLimite == '' or $diasLimite == 0) {
				 	  													 		echo 'Sin Limite';
				 		  													 }else{
				 		  													 	if ($hoy > $fechaLimite) {?>
				 		  													 		<a href="#" onclick="fnMotivoVencimientoCanasta('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>','<?php echo $codEtapa ?>','<?php echo $idEtapa ?>','<?php echo $idDerivacionCanasta ?>','<?php echo $rutPrestador ?>')"><span class="badge badge-danger"><font size="2"><?php echo $diff->days.' dias vencida' ?> <i class="fas fa-comment-dots"></i></font></span></a>
				 		  													 	<?php }else{ ?>
				 		  													 	<span class="badge badge-success"><font size="2"><?php echo $diff->days;?> de <?php echo $diasLimite ?></font></span>
				 		  													 	<?php }
				 		  													 }
		 	  													 		}
			  													?>
			  											</td>
			  											<td style="text-align: center;">
			  												<?php if ($qrCanastaPatologia->Fields('ESTADO') == 'activa') { 
			  														if ($qrCanastaPatologia->Fields('INICIAL') == 'no') {
			  															if ($tipoPerfil == 2) {?>			  																

			  																<button class="btn btn-sm btn-danger" onclick="fnCargaEliminarCanasta('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>','<?php echo $codEtapa ?>','<?php echo $idEtapa ?>','<?php echo $idDerivacionCanasta ?>','<?php echo $rutPrestador ?>','<?php echo $idBitacora ?>')"><i class="far fa-trash-alt"></i></button>
			  															<?php }
			  														} ?>
			  													<button class="btn btn-xs btn-warning" onclick="fnFinalizarCanasta('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>','<?php echo $codEtapa ?>','<?php echo $idEtapa ?>','<?php echo $idDerivacionCanasta ?>','<?php echo $rutPrestador ?>')">Fin</button>
			  												<?php }else{ ?>
			  													<span class="badge badge-success">Finalizada<font size="2"></font></span> 
			  												<?php } ?> 
			  												
			  											</td>
			  										</tr>
						  						<?php	
						  						$i++;
						  						$qrCanastaPatologia->MoveNext();
					  						 	 } ?>
				  						 	</table>
				  						 	</div>
					  					</td>
					  				</tr>
				  				   	<?php 	$qrDerivacionEtapa->MoveNext();
				  						 } ?>
								</table>
							</div>
						</div>
					</div>  
					<div class="modal-footer" align="right">	
			  	</div>     
	  		</div>
	</body>
</html>

<script>

function fnfrmAgregarEtapa(idDerivacion){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmAgregarEtapa.php?idDerivacion=' + idDerivacion);
}

function fnfrmAgregarCanasta(idDerivacion,etapaPatologia,idEtapaPatologia){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmAgregarCanasta.php?idDerivacion=' + idDerivacion +'&etapaPatologia=' + etapaPatologia +'&idEtapaPatologia=' + idEtapaPatologia);
}

function fnFinalizarCanasta(idDerivacion,etapaPatologia,idEtapaPatologia,idDerivacionCanasta,prestador){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmCambiaEstadoCanasta.php?idDerivacion=' + idDerivacion +'&etapaPatologia=' + etapaPatologia +'&idEtapaPatologia=' + idEtapaPatologia +'&idDerivacionCanasta=' + idDerivacionCanasta +'&prestador=' + prestador);
}

function fnMotivoVencimientoCanasta(idDerivacion,etapaPatologia,idEtapaPatologia,idDerivacionCanasta,prestador){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmMotivoVencimientoCanasta.php?idDerivacion=' + idDerivacion +'&etapaPatologia=' + etapaPatologia +'&idEtapaPatologia=' + idEtapaPatologia +'&idDerivacionCanasta=' + idDerivacionCanasta +'&prestador=' + prestador);
}

function fnAgregarPrestadorCanasta(idDerivacion,etapaPatologia,idEtapaPatologia,idDerivacionCanasta){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmAgregarPrestadorCanasta.php?idDerivacion=' + idDerivacion +'&etapaPatologia=' + etapaPatologia +'&idEtapaPatologia=' + idEtapaPatologia +'&idDerivacionCanasta=' + idDerivacionCanasta);
}

function fnCargaEliminarCanasta(idDerivacion,etapaPatologia,idEtapaPatologia,canasta,prestador,idBitacora){
	$('#dvfrmAgregarEtapaoCanasta').load('vistas/modulos/derivacion/modals/frmEliminaCanasta.php?idDerivacion=' + idDerivacion +'&etapaPatologia=' + etapaPatologia +'&idEtapaPatologia=' + idEtapaPatologia +'&canasta=' + canasta +'&prestador=' + prestador+'&idBitacora=' + idBitacora);
}

function fnSiNoEliminaAdjunto(idDerivacion, idBitacora, ruta) {


		Swal.fire({
	  title: 'Estas Seguro?',
	  text: "No podras revertir la eliminación!",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Si, Eliminar!'
	}).then((result) => {
	  if (result.isConfirmed) {
	  	fnEliminarAdjunto(idDerivacion, idBitacora, ruta)
	    
	  } 
	})
}

function fnEliminarAdjunto(idDerivacion, idBitacora, ruta){

	cadena = 'idDerivacion=' + idDerivacion +
			 '&ruta=' + ruta +
			 '&idBitacora=' + idBitacora;
		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacora/adjuntaDoc/docs/eliminaAdjunto.php',
			success:function(r){
				if (r == 1) {
					swal("Todo bien!", "Se a eliminado el adjunto", "success");
					$('#dvfrmDetalleDerivacion').load('vistas/modulos/derivacion/frmDetalleDerivacion.php?idDerivacion=' + idDerivacion);
	    	}
				
			}
		});

}


	
</script>



