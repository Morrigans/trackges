<?php
//Connection statement
require_once '../../Connections/oirs.php';
//Aditional Functions
require_once '../../includes/functions.inc.php';

session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../index.php');
exit; }
//require_once('modals/informacionPaciente/modalInformacionPacienteGestora.php');

$usuario = $_SESSION['dni'];

$query_qrDerivacion = "SELECT
derivaciones.COD_RUTPAC,
derivaciones.N_DERIVACION,
derivaciones.FECHA_DERIVACION,
derivaciones.CODIGO_PATOLOGIA,
derivaciones_canastas.FECHA_CANASTA,
prevision.PREVISION,
pacientes.NOMBRE,
pacientes.SEXO,
pacientes.FEC_NACIMI,
pacientes.FONO,
regiones.region_nombre,
provincias.provincia_nombre,
comunas.comuna_nombre,
tipo_patologia.DESC_TIPO_PATOLOGIA,
prestador.DESC_PRESTADOR


FROM derivaciones,derivaciones_canastas,pacientes,prestador,tipo_patologia,prevision,regiones,provincias,comunas

WHERE 
derivaciones.ID_DERIVACION = derivaciones_canastas.ID_DERIVACION AND
derivaciones_canastas.INICIAL = 'si' AND
derivaciones.COD_RUTPAC = pacientes.COD_RUTPAC AND
derivaciones.RUT_PRESTADOR = prestador.ID_PRESTADOR AND
derivaciones.CODIGO_TIPO_PATOLOGIA = tipo_patologia.ID_TIPO_PATOLOGIA AND
derivaciones.ID_CONVENIO = prevision.ID AND
pacientes.REGION = regiones.region_id AND
pacientes.PROVINCIA = provincias.provincia_id AND
pacientes.COMUNA = comunas.comuna_id";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

?>

<div class="table-responsive-sm">
<table id="tDerivaciones" class="table table-bordered table-striped table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">derivacion</font></th>
			<th><font size="2">rut_paciente</font></th>
			<th><font size="2">nombre_pcte</font></th>
			<th><font size="2">prevision</font></th>
			<th><font size="2">sexo</font></th>
			<th><font size="2">fecha_nacimiento</font></th>
			<th><font size="2">telefono</font></th>
			<th><font size="2">region</font></th>
			<th><font size="2">provincia</font></th>
			<th><font size="2">comuna</font></th> 
			<th><font size="2">fecha_derivacion</font></th>
			<th><font size="2">fecha_activacion</font></th>
			<th><font size="2">tipo_patologia</font></th>
			<th><font size="2">prestador_derivado</font></th>
			<th><font size="2">patologia</font></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$n=1;
	while (!$qrDerivacion->EOF) {
		$codPatologia = $qrDerivacion->Fields('CODIGO_PATOLOGIA');

		$query_qrPatologia = "SELECT * FROM patologia WHERE CODIGO_PATOLOGIA= '$codPatologia'";
		$qrPatologia = $oirs->SelectLimit($query_qrPatologia) or die($oirs->ErrorMsg());
		$totalRows_qrPatologia = $qrPatologia->RecordCount();
	?>
		<tr>
			<td><font size="2"><?php echo $qrDerivacion->Fields('N_DERIVACION') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('COD_RUTPAC') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('NOMBRE') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('PREVISION') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('SEXO') ?></font></td>
			<td><font size="2"><?php 
				if ($qrDerivacion->Fields('FEC_NACIMI') == '' or $qrDerivacion->Fields('FEC_NACIMI') == '0000-00-00' or $qrDerivacion->Fields('FEC_NACIMI') == null or $qrDerivacion->Fields('FEC_NACIMI') == '1969-12-31') {
					echo '0000-00-00';
				}else{
					echo $qrDerivacion->Fields('FEC_NACIMI');
				}
			?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('FONO') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('region_nombre') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('provincia_nombre') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('comuna_nombre') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('FECHA_DERIVACION') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('FECHA_CANASTA') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('DESC_TIPO_PATOLOGIA') ?></font></td>
			<td><font size="2"><?php echo $qrDerivacion->Fields('DESC_PRESTADOR') ?></font></td>
			<td><font size="2"><?php echo $qrPatologia->Fields('DESC_PATOLOGIA') ?></font></td>
			
		</tr>
		<?php
		$n++;
	 	$qrDerivacion->MoveNext();
	}
	?>
	</tbody>
</table>
</div>
<script>
		$(function () {
		    $('#tDerivaciones').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": true,
		      "responsive": true,
		      "order": [[ 9, 'desc' ]],
		      dom: 'lBfrtip',
				    "language": {
			        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
			        },
			  
		    });

		  });
</script>