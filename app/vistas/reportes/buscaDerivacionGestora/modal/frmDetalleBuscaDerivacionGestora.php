<?php
//Connection statement
require_once '../../../../Connections/oirs.php';
//Aditional Functions
require_once '../../../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }


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

$codRutPac = $qrPaciente->Fields('COD_RUTPAC');


$codTipoPatologia = $qrDerivacion->Fields('CODIGO_TIPO_PATOLOGIA');

$query_qrTipoPatologia= "SELECT * FROM $MM_oirs_DATABASE.tipo_patologia WHERE ID_TIPO_PATOLOGIA = '$codTipoPatologia'";
$qrTipoPatologia = $oirs->SelectLimit($query_qrTipoPatologia) or die($oirs->ErrorMsg());
$totalRows_qrTipoPatologia = $qrTipoPatologia->RecordCount();

$codPatologia = $qrDerivacion->Fields('ID_PATOLOGIA');

$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA = '$codPatologia'";
$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
$totalRows_qrPatologia = $qrPatologia->RecordCount();

$codEtapaPatologia = $qrDerivacion->Fields('CODIGO_ETAPA_PATOLOGIA');





$codConvenio = $qrDerivacion->Fields('ID_CONVENIO');

$query_qrConvenio= "SELECT * FROM $MM_oirs_DATABASE.prevision WHERE ID = '$codConvenio'";
$qrConvenio = $oirs->SelectLimit($query_qrConvenio) or die($oirs->ErrorMsg());
$totalRows_qrConvenio = $qrConvenio->RecordCount();

$query_qrDerivacionCanasta= "SELECT * FROM $MM_oirs_DATABASE.derivaciones_canastas WHERE ID_DERIVACION ='$idDerivacion'";
$qrDerivacionCanasta = $oirs->SelectLimit($query_qrDerivacionCanasta) or die($oirs->ErrorMsg());
$totalRows_qrDerivacionCanasta = $qrDerivacionCanasta->RecordCount();




?>

	<header class="">
		
		<p align="center"><font size="5">DERIVACIÓN <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?></font><br>
		<br>
	</header>
	<div class="container">
		<fieldset>
			<br>
			<table width="100%" align="center">
				<tr>
					<!-- <td><div><strong>N° DERIVACION:</strong> <?php echo $qrDerivacion->Fields('N_DERIVACION'); ?></div></td>	 -->								
				</tr>
				<tr>
					<td><div><strong>NOMBRE DEL PACIENTE:</strong> <?php echo $qrPaciente->Fields('NOMBRE'); ?></div></td>										
				</tr>
				<tr>
					<td><div><strong>RUT:</strong><?php echo $codRutPac; ?></div></td>									
				</tr>
				<tr>
					<td><div><strong>PREVISION:</strong> <?php echo $qrConvenio->Fields('PREVISION'); ?></div></td>										
				</tr>
				<tr>					
					<td><div><strong>TIPO PATOLOGIA:</strong> <?php echo $qrTipoPatologia->Fields('DESC_TIPO_PATOLOGIA'); ?> </div></td>				
				</tr>
				<tr>					
					<td><div><strong>PATOLOGIA:</strong> <?php echo $qrPatologia->Fields('DESC_PATOLOGIA'); ?> </div></td>				
				</tr>
				<tr>					
					<td><div><strong>ESTADO:</strong> <?php echo $qrDerivacionCanasta->Fields('ESTADO'); ?> </div></td>				
				</tr>
				
			</table>
		</fieldset>

	</div>
	<br>
	<div class="table-responsive-sm"> 
			<table id="tblPatologias" class="table table-bordered table-striped table-hover table-sm">
					<thead class="table-dark">
						<tr align="center">
							<th>Canasta</th>
							<th>Etapa</th>
							<th>Fecha Canasta</th>
							<th>Fecha Limite</th>
							<th>Fecha Finalizada</th>
							<th>Prestador</th>
					
						</tr>
					</thead>
					<tbody>
						<?php
					while (!$qrDerivacionCanasta->EOF) {
				 $codCanastaPatologia = $qrDerivacionCanasta->Fields('CODIGO_CANASTA_PATOLOGIA');
				 $codEtapaPatologia = $qrDerivacionCanasta->Fields('CODIGO_ETAPA_PATOLOGIA');
				 $rutPrestador = $qrDerivacionCanasta->Fields('RUT_PRESTADOR');

				$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
				$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
				$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

				$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
				$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
				$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

				$query_qrPrestador= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR='$rutPrestador'";
				$qrPrestador = $oirs->SelectLimit($query_qrPrestador) or die($oirs->ErrorMsg());
				$totalRows_qrPrestador = $qrPrestador->RecordCount();


					 
					 ?>

						<tr>
						<td><?php echo $qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA'); ?></td>
						<td><?php echo $qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA'); ?></td>
						<td><?php echo date("d-m-Y",strtotime($qrDerivacionCanasta->Fields('FECHA_CANASTA'))); ?></td>
						<td><?php echo date("d-m-Y",strtotime($qrDerivacionCanasta->Fields('FECHA_LIMITE'))); ?></td>
						<td><?php echo date("d-m-Y",strtotime($qrDerivacionCanasta->Fields('FECHA_FIN_CANASTA'))); ?></td>
						<td><?php echo $qrPrestador->Fields('DESC_PRESTADOR'); ?></td>
						 
						</tr>
						<?php
			   	 	$qrDerivacionCanasta->MoveNext();
					}
					?>
					</tbody>
				</table>	 
	</div> 

<script>


// $(function () {
// 	    $('#tDatosPac').DataTable({
// 	      "paging": false,
// 	      "lengthChange": false,
// 	      "searching": true,
// 	      "ordering": true,
// 	      "info": false,
// 	      "autoWidth": true,
// 	      "responsive": true,
// 	      "order": [[ 2, 'desc' ]],
// 	      dom: 'lBfrtip',
// 		    buttons: [ 'excel', 'pdf'],
// 		    "language": {
// 		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
// 		        }
// 	    });

// 	  });
	
</script>



