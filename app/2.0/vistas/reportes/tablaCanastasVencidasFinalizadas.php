<?php
//Connection statement
require_once '../../Connections/oirs.php';
//Aditional Functions
require_once '../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$usuario = $_SESSION['dni'];

$query_qrLogin= "SELECT * FROM $MM_oirs_DATABASE.login WHERE USUARIO = '$usuario'";
$qrLogin = $oirs->SelectLimit($query_qrLogin) or die($oirs->ErrorMsg());
$totalRows_qrLogin = $qrLogin->RecordCount();

//obtengo el tipo de usuario
$perfil =$qrLogin->Fields('TIPO');

//si es gestora filtro por rut de usuario para mostrar solo sus derivaciones, sino muestro todo al administrador y supervisor
if ($perfil == '3') {//gestora
	$query_qrVencecidas = "SELECT 
		$MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.N_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.ESTADO,
		$MM_oirs_DATABASE.derivaciones.FECHA_DERIVACION,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE,
		$MM_oirs_DATABASE.derivaciones.COD_RUTPAC,
		$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.CODIGO_CANASTA_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.CODIGO_ETAPA_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.MOTIVO_FIN_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.OBSERVACION,
		$MM_oirs_DATABASE.derivaciones_canastas.RUT_PRESTADOR,
		$MM_oirs_DATABASE.derivaciones.CODIGO_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones.ID_CONVENIO, 
		$MM_oirs_DATABASE.derivaciones.REASIGNADA,
		$MM_oirs_DATABASE.derivaciones.ENFERMERA 
	FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
	where
	$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
	$MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA > $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE and
	$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
	$MM_oirs_DATABASE.derivaciones_canastas.ESTADO = 'finalizada' and 
	$MM_oirs_DATABASE.derivaciones.ENFERMERA = '$usuario'";
	$qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
	$totalRows_qrVencecidas = $qrVencecidas->RecordCount();
}else{
	$query_qrVencecidas = "SELECT 
		$MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.N_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.ID_DERIVACION,
		$MM_oirs_DATABASE.derivaciones.ESTADO,
		$MM_oirs_DATABASE.derivaciones.FECHA_DERIVACION,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE,
		$MM_oirs_DATABASE.derivaciones.COD_RUTPAC,
		$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.CODIGO_CANASTA_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.CODIGO_ETAPA_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones_canastas.MOTIVO_FIN_CANASTA,
		$MM_oirs_DATABASE.derivaciones_canastas.OBSERVACION,
		$MM_oirs_DATABASE.derivaciones_canastas.RUT_PRESTADOR,
		$MM_oirs_DATABASE.derivaciones.CODIGO_PATOLOGIA,
		$MM_oirs_DATABASE.derivaciones.ID_CONVENIO, 
		$MM_oirs_DATABASE.derivaciones.REASIGNADA,
		$MM_oirs_DATABASE.derivaciones.ENFERMERA 
	FROM $MM_oirs_DATABASE.derivaciones, $MM_oirs_DATABASE.derivaciones_canastas
	where
	$MM_oirs_DATABASE.derivaciones.ID_DERIVACION =  $MM_oirs_DATABASE.derivaciones_canastas.ID_DERIVACION and
	$MM_oirs_DATABASE.derivaciones_canastas.FECHA_FIN_CANASTA > $MM_oirs_DATABASE.derivaciones_canastas.FECHA_LIMITE and
	$MM_oirs_DATABASE.derivaciones.CODIGO_TIPO_PATOLOGIA = '1' and
	$MM_oirs_DATABASE.derivaciones_canastas.ESTADO = 'finalizada'";
	$qrVencecidas = $oirs->SelectLimit($query_qrVencecidas) or die($oirs->ErrorMsg());
	$totalRows_qrVencecidas = $qrVencecidas->RecordCount();
}
?>
<div class="table-responsive-sm">
<table id="tbVencidasFinalizadas" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">N° Derivacion</font></th>
			<th><font size="2">Rut</font></th>
			<th><font size="2">Paciente</font></th>
			<th><font size="2">Patología</font></th>
			<th><font size="2">Etapa</font></th>
			<th><font size="2">Canasta</font></th>
			<th><font size="2">Prestador</font></th>
			<th><font size="2">Fecha Canasta</font></th>
			<th><font size="2">Fecha Fin Canasta</font></th>
			<th><font size="2">Fecha Limite</font></th>
			<th><font size="2">Observación</font></th>
			<th><font size="2">Motivo</font></th>
			<th><font size="2">Orden</font></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$n=1;
	while (!$qrVencecidas->EOF) {
		$codRutPac=$qrVencecidas->Fields('COD_RUTPAC');
		$motivoFinCanasta=$qrVencecidas->Fields('MOTIVO_FIN_CANASTA');

		$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
		$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
		$totalRows_qrPaciente = $qrPaciente->RecordCount();

		$query_qrMotivoFinCanasta= "SELECT * FROM $MM_oirs_DATABASE.motivos_fin_canastas WHERE ID_MOTIVO = '$motivoFinCanasta'";
		$qrMotivoFinCanasta = $oirs->SelectLimit($query_qrMotivoFinCanasta) or die($oirs->ErrorMsg());
		$totalRows_qrMotivoFinCanasta = $qrMotivoFinCanasta->RecordCount();

		$codPatologia = $qrVencecidas->Fields('CODIGO_PATOLOGIA');

		$query_qrPatologia= "SELECT * FROM $MM_oirs_DATABASE.patologia WHERE CODIGO_PATOLOGIA = '$codPatologia'";
		$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrPatologia = $qrPatologia->RecordCount();

		$codEtapaPatologia = $qrVencecidas->Fields('CODIGO_ETAPA_PATOLOGIA');

		$query_qrEtapaPatologia= "SELECT * FROM $MM_oirs_DATABASE.etapa_patologia WHERE CODIGO_ETAPA_PATOLOGIA = '$codEtapaPatologia'";
		$qrEtapaPatologia = $oirs->SelectLimit($query_qrEtapaPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrEtapaPatologia = $qrEtapaPatologia->RecordCount();

		$codCanastaPatologia = $qrVencecidas->Fields('CODIGO_CANASTA_PATOLOGIA');

		$query_qrCanastaPatologia= "SELECT * FROM $MM_oirs_DATABASE.canasta_patologia WHERE CODIGO_CANASTA_PATOLOGIA = '$codCanastaPatologia'";
		$qrCanastaPatologia = $oirs->SelectLimit($query_qrCanastaPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrCanastaPatologia = $qrCanastaPatologia->RecordCount();

		$rutPrestador = $qrVencecidas->Fields('RUT_PRESTADOR');

		$query_qrPrestador= "SELECT * FROM $MM_oirs_DATABASE.prestador WHERE RUT_PRESTADOR = '$rutPrestador'";
		$qrPrestador = $oirs->SelectLimit($query_qrPrestador) or die($oirs->ErrorMsg());
		$totalRows_qrPrestador = $qrPrestador->RecordCount();

	?>
		<tr>
			<td><span class="badge badge-warning"><font size="3"><?php echo $qrVencecidas->Fields('N_DERIVACION'); ?></font></span></td>
			<td>
				<font size="2">
					<?php 
						$codRutPac = explode(".", $qrVencecidas->Fields('COD_RUTPAC'));
						$rut0 = $codRutPac[0]; // porción1
						$rut1 = $codRutPac[1]; // porción2
						$rut2 = $codRutPac[2]; // porción2
						$codRutPac = $rut0.$rut1.$rut2;
						echo $codRutPac;
					?>
				</font>
			</td>
			<td><font size="1"><b><?php echo utf8_encode(strtoupper($qrPaciente->Fields('NOMBRE'))); ?></b></font></td>
			<td><span><font size="2"><?php echo utf8_encode($qrPatologia->Fields('DESC_PATOLOGIA')); ?></font></span></td>			
			<td><span><font size="2"><?php echo utf8_encode($qrEtapaPatologia->Fields('DESC_ETAPA_PATOLOGIA')); ?></font></span></td>			
			<td><span><font size="2"><b><?php echo utf8_encode($qrCanastaPatologia->Fields('DESC_CANASTA_PATOLOGIA')); ?></b></font></span></td>			
			<td><span><font size="2"><?php echo utf8_encode($qrPrestador->Fields('DESC_PRESTADOR')); ?></font></span></td>			
			<td><span><font size="2"><?php echo date("d-m-Y",strtotime($qrVencecidas->Fields('FECHA_CANASTA'))); ?></font></span></td>			
			<td><span><font size="2"><?php echo date("d-m-Y",strtotime($qrVencecidas->Fields('FECHA_FIN_CANASTA'))); ?></font></span></td>			
			<td><span><font size="2"><?php echo date("d-m-Y",strtotime($qrVencecidas->Fields('FECHA_LIMITE'))); ?></font></span></td>			
			<td><font size="2"><?php echo utf8_encode($qrVencecidas->Fields('OBSERVACION')); ?></font></td>
			<td><font size="2"><?php echo utf8_encode($qrMotivoFinCanasta->Fields('DESC_MOTIVO')); ?></font></td>
			<td><font size="2"><?php echo $n; ?></font></td>
		</tr>
		<?php
			$n++;
	 	$qrVencecidas->MoveNext();
	}
	?>
	</tbody>
</table>
</div>

<script type="text/javascript">
	
		$(function () {
	    $('#tbVencidasFinalizadas').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": true,
	      "responsive": true,
	      "order": [[ 11, 'desc' ]],
	      dom: 'lBfrtip',
		    buttons: [ 'copy', 'excel' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });
	  });
	
</script>