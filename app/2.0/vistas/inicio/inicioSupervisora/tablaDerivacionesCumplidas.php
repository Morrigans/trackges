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
//require_once('modals/informacionPaciente/modalEditaInformacionPacienteSupervisora.php');

$query_qrDerivacion = "SELECT
	DISTINCT($MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION),
	$MM_oirs_DATABASE.derivaciones.N_DERIVACION,
	$MM_oirs_DATABASE.derivaciones.ID_DERIVACION,
	$MM_oirs_DATABASE.derivaciones.ESTADO,
	$MM_oirs_DATABASE.derivaciones.FECHA_DERIVACION,
	$MM_oirs_DATABASE.derivaciones.FECHA_LIMITE,
	$MM_oirs_DATABASE.derivaciones.COD_RUTPAC,
	$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA,
	$MM_oirs_DATABASE.derivaciones_canastas.CODIGO_CANASTA_PATOLOGIA,
	$MM_oirs_DATABASE.derivaciones_canastas.FECHA_CANASTA,
	$MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA,
	$MM_oirs_DATABASE.derivaciones.CODIGO_PATOLOGIA,
	$MM_oirs_DATABASE.derivaciones.ID_CONVENIO, 
	$MM_oirs_DATABASE.derivaciones.REASIGNADA,
	$MM_oirs_DATABASE.derivaciones.ENFERMERA 
FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
where
$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
$MM_oirs_DATABASE.derivaciones_canastas.ESTADO = 'finalizada' group by derivaciones_canastas.ID_DERIVACION";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

?>
<div class="table-responsive-sm">
<table id="tPacientesDerivados" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Opciones</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Estado</font></th>
			<th><font size="2">Fecha derivación</font></th>
			<!-- <th><font size="2">Fecha limite</font></th> -->
			<th><font size="2">Rut paciente</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Tipo</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Etapa patología</font></th>
			<th><font size="2">Canasta patología</font></th> 
			<th><font size="2">Prestador</font></th> 
			<th><font size="2">Isapre</font></th> 
			<th><font size="2">N°</font></th>
			
		</tr>
	</thead>
	<tbody>
		<?php
		$n=1;
	while (!$qrDerivacion->EOF) {

		$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

		//capturo fecha de inicio de canasta de las canastas finalizadas
		$fechaInicioCanastaPatologia = $qrDerivacion->Fields('FECHA_CANASTA');

		$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
		$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

		//capturo los dias de tiempo limite de cada canasta finalizada
		$diasGarantia = $qrCanastaPatologia->Fields('TIEMPO_LIMITE');

		if ($diasGarantia == '') {
			$LimiteCanasta = '2100-01-01'; //para canastas que no tienen limite
		}else{	
		//somo dias limite a fecha de inicio canasta para saber si cumplio o no
		$LimiteCanasta = date("Y-m-d",strtotime($fechaInicioCanastaPatologia."+ $diasGarantia days"));
		}

		$fechaFinCanasta = $qrDerivacion->Fields('FECHA_FIN_CANASTA');

		if ($fechaFinCanasta <= $LimiteCanasta) {
			$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');
			$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');
			$codCanastaPatologia;
			$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
			$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
			$totalRows_qrPaciente = $qrPaciente->RecordCount();

			$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

			$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
			$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
			$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

			$codPatologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

			$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA = '$codPatologia'";
			$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
			$totalRows_qrPatologia = $qrPatologia->RecordCount();

			$query_qrPrestadorAsig= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='finalizada'";
			$qrPrestadorAsig = $oirs->SelectLimit($query_qrPrestadorAsig) or die($oirs->ErrorMsg());
			$totalRows_qrPrestadorAsig = $qrPrestadorAsig->RecordCount();


			$query_qrDerivacionCanasta= "SELECT DISTINCT(ID_DERIVACION), CODIGO_CANASTA_PATOLOGIA FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='finalizada' AND FECHA_FIN_CANASTA <= '$LimiteCanasta'";
			$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
			$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();

			$query_qrDerivacionEtapa= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_etapas WHERE ID_DERIVACION = '$idDerivacion'";
			$qrDerivacionEtapa = $oirs->SelectLimit($query_qrDerivacionEtapa) or die($oirs->ErrorMsg());
			$totalRows_qrDerivacionEtapa = $qrDerivacionEtapa->RecordCount();

			?>
			<tr>
				<td>
					<div class="btn-group">
						<button type="button" class="btn btn-default"><i class="fas fa-cog"></i></button>
						<button type="button" class="btn btn-default dropdown-toggle dropdown-icon" data-toggle="dropdown"></button>
						<div class="dropdown-menu" role="menu">
							<?php if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'no') {
								$colorEstado = 'badge badge-primary';?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAceptarCasoGestora" onclick="fnfrmAceptarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Aceptar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php } 

							 //si viene reasignada sin prestador asignado
							 if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') == '') {
								$colorEstado = 'badge badge-primary';?>
								<!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarPrestadorCaso" onclick="fnfrmAsignarPrestadorCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar prestador</a> -->
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAceptarCasoGestora" onclick="fnfrmAceptarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Aceptar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a> 
							<?php } 

							//si viene reasignada con prestador asignado
							 if ($qrDerivacion->Fields('ESTADO') == 'pendiente' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') != '') {
								$colorEstado = 'badge badge-primary';?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAceptarCasoGestora" onclick="fnfrmAceptarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Aceptar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php } 

							if ($qrDerivacion->Fields('ESTADO') == 'aceptada' and $qrDerivacion->Fields('REASIGNADA') == 'no') { //no reasignada
								$colorEstado = 'badge badge-info';
								?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarPrestadorCasoGestora" onclick="fnfrmAsignarPrestadorCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar prestador</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php
							}

							//reasignada sin prestador
							if ($qrDerivacion->Fields('ESTADO') == 'aceptada' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') == '') { 
								$colorEstado = 'badge badge-info';
								?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalAsignarPrestadorCasoGestora" onclick="fnfrmAsignarPrestadorCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Asignar prestador</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacionGestora" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a> -->
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php
							}

							//reasignada con prestador
							if ($qrDerivacion->Fields('ESTADO') == 'aceptada' and $qrDerivacion->Fields('REASIGNADA') == 'si' and $qrDerivacion->Fields('RUT_PRESTADOR') != '') { 
								$colorEstado = 'badge badge-info';
								?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacionGestora" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php
							}

							if ($qrDerivacion->Fields('ESTADO') == 'cerrada') {
								$colorEstado = 'badge badge-danger';
							}

							if ($qrDerivacion->Fields('ESTADO') == 'prestador') {
								$colorEstado = 'badge badge-success';
								?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalDerivacionGestora" onclick="fnfrmDetalleDerivacion('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Etapas/Canastas</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalReasignarCasoGestora" onclick="fnfrmReasignarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Reasignar Caso</a>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCerrarCasoGestora" onclick="fnfrmCerrarCasoGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Cerrar Caso</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
							<?php
							}?>
								<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
								<a class="dropdown-item" target="_BLANK" href="2.0/vistas/inicio/inicioSupervisora/modals/enviaInfoPacACorreo/detallePacientePdf.php?idDerivacion=<?php echo $idDerivacion ?>&codRutPac=<?php echo $codRutPac ?>">Genera Pdf</a> 

						</div>
					</div>
				</td>
				<td><span class="badge badge-warning"><font size="3"><?php echo $qrDerivacion->Fields('N_DERIVACION'); ?></font></span></td>
				<td><span class="<?php echo $colorEstado ?>"><font size="1"><?php echo $qrDerivacion->Fields('ESTADO'); ?></font></span></td>
				<td><font size="2"><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></font></td>
				<!-- <td>
					<font size="2">
						<?php
						if ($qrDerivacion->Fields('FECHA_LIMITE') == '0000-00-00' or $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA') == 'CAEC') {
							echo 'Sin Limite';
						}else{
							echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_LIMITE')));
						} ?>
					</font>
				</td> -->
				<td>
					<font size="2">
						<?php 
							$codRutPac = explode(".", $codRutPac);
							$rut0 = $codRutPac[0]; // porción1
							$rut1 = $codRutPac[1]; // porción2
							$rut2 = $codRutPac[2]; // porción2
							$codRutPac = $rut0.$rut1.$rut2;
							echo $codRutPac;
						?>
					</font>
				</td>
				<td><font size="1"><b><?php echo utf8_encode(strtoupper($qrPaciente->Fields('NOMBRE'))); ?></b></font></td>
				<td><font size="2"><?php echo $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA'); ?></font></td>
				<td><font size="2"><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></font></td>
				<td>
					<font size="2">
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

									$query_qrBuscaCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_ETAPA_PATOLOGIA = '$idEtapaPatologia' AND ID_DERIVACION = '$idDerivacion' and ESTADO ='finalizada' AND FECHA_FIN_CANASTA <= '$LimiteCanasta'";
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
					</font>
				</td>
				<td>
					<font size="2">
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
					</font>
				</td>
				<td>
					<font size="2">
						<?php  
						$i=1;
						while (!$qrPrestadorAsig->EOF) {
							$rutPrestador = $qrPrestadorAsig->Fields('RUT_PRESTADOR');

							$query_qrPrestador= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
							$qrPrestador = $oirs->SelectLimit($query_qrPrestador) or die($oirs->ErrorMsg());
							$totalRows_qrPrestador = $qrPrestador->RecordCount();

							if ($rutPrestador == '') {
								echo $i.'.- '.'Sin prestador.'.'</br>';
							}else{
								echo $i.'.- '.utf8_encode($qrPrestador->Fields('DESC_PRESTADOR')).'.</br>';
							}

							$i++;
						$qrPrestadorAsig->MoveNext();
						}
						
						?>
					</font>
				</td>
				<td>
					<font size="2">
						<?php

						$idPrevision = $qrDerivacion->Fields('ID_CONVENIO');

						$query_previ = "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$idPrevision'";
						$previ = $oirs->SelectLimit($query_previ) or die($oirs->ErrorMsg());
						$totalRows_previ = $previ->RecordCount();

						echo $prevision = $previ->Fields('PREVISION'); ?>
					</font>
				</td>
				<td><font size="2"><?php echo $n; ?></font></td> 
			</tr>
		<?php } ?>


		



		
		<?php
		$n++;
	 	$qrDerivacion->MoveNext();
	}
	?>
	</tbody>
</table>
</div>

<script type="text/javascript">
	
	$(function () {
	    $('#tPacientesDerivados').DataTable({
	      "paging": true,
	      "lengthChange": true,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 12, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [
		                {
		                    extend: 'excelHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                    }
		                },
		                {
		                    extend: 'pdfHtml5',
		                    exportOptions: {
		                        columns: [ 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12 ]
		                    }
		                }
		                
		            ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        },
		    // "columnDefs": [
      //               {
      //                   "targets": [ 1 ],
      //                   "visible": false,
      //                   "searchable": false
      //               }
                   
      //           ]
	    });

	  });
</script>