<?php
//Connection statement
require_once '../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../includes/functions.inc.php';
require_once('modal/modalBuscaDerivacionGestora.php');
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }




 $rutGestora = $_REQUEST['rutGestora'];


	$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.derivaciones WHERE ENFERMERA='$rutGestora'";
	$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
	$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

?>
<div class="table-responsive-sm">
<table id="tbBuscaDerivacionGestora" class="table table-bordered table-striped table-hover table-sm">
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
			<th><font size="2">Isapre</font></th> 
			<th><font size="2">N°</font></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$n=1;
	while (!$qrDerivacion->EOF) {
		$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');

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

		$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');

		$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
		$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

		$codCanastaPatologia = $qrDerivacion->Fields('CODIGO_CANASTA_PATOLOGIA');

		$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
		$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

		$idDerivacion = $qrDerivacion->Fields('ID_DERIVACION');

		$query_qrPrestadorAsig= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
		$qrPrestadorAsig = $oirs->SelectLimit($query_qrPrestadorAsig) or die($oirs->ErrorMsg());
		$totalRows_qrPrestadorAsig = $qrPrestadorAsig->RecordCount();
		
		$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION = '$idDerivacion' and ESTADO ='activa'";
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

							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBuscaDerivacionGestora" onclick="fnfrmBuscaDerivacionGestora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Detalle</a>
							<a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrDerivacion->Fields('ID_DERIVACION') ?>')">Bitacora</a>
						
					</div>
				</div>
			</td>
			<td><span class="badge badge-warning"><font size="3"><?php echo $qrDerivacion->Fields('N_DERIVACION'); ?></font></span></td>
			<td><span class="<?php echo $colorEstado ?>"><font size="2"><?php echo $qrDerivacion->Fields('ESTADO'); ?></font></span></td>
			<td><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></td>
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
				<font size="3">
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
			<td><font size="2"><b><?php echo utf8_encode(strtoupper($qrPaciente->Fields('NOMBRE'))); ?></b></font></td>
			 <td><font size="3"><?php echo $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA'); ?></font></td>
			<td><font size="3"><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></font></td>
			<td>
				<font size="3">
					<?php

					$idPrevision = $qrDerivacion->Fields('ID_CONVENIO');

					$query_previ = "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$idPrevision'";
					$previ = $oirs->SelectLimit($query_previ) or die($oirs->ErrorMsg());
					$totalRows_previ = $previ->RecordCount();

					echo $prevision = $previ->Fields('PREVISION'); ?>
				</font>
			</td> 
			<td><font size="3"><?php echo $n; ?></font></td> 
			
		</tr>
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
	    $('#tbBuscaDerivacionGestora').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 1, 'asc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'copy', 'excel', 'csv' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });
	  });

	function fnfrmBuscaDerivacionGestora(idDerivacion){
	
	    $('#dvBuscaDerivacionGestora').load('vistas/reportes/buscaDerivacionGestora/modal/frmDetalleBuscaDerivacionGestora.php?idDerivacion=' + idDerivacion);
	}
</script>