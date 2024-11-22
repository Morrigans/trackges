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

$query_qrDerivacion = "SELECT * FROM $MM_oirs_DATABASE.2_derivaciones WHERE ID_DERIVACION = '$idDerivacion'";
$qrDerivacion = $oirs->SelectLimit($query_qrDerivacion) or die($oirs->ErrorMsg());
$totalRows_qrDerivacion = $qrDerivacion->RecordCount();

$codRutPac = $qrDerivacion->Fields('COD_RUTPAC');
$enfermera = $qrDerivacion->Fields('ENFERMERA');
$estadoRn = utf8_encode($qrDerivacion->Fields('ESTADO_RN'));

$query_qrPaciente= "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC = '$codRutPac'";
$qrPaciente = $oirs->SelectLimit($query_qrPaciente) or die($oirs->ErrorMsg());
$totalRows_qrPaciente = $qrPaciente->RecordCount();

?>
<div class="col-md-12">
	<div class="table-responsive">
			<table class="table">
				<tr>
					<th style="">Folio Right Now:</th>
					<td>
						<?php echo utf8_encode($qrDerivacion->Fields('FOLIO')); ?>
					</td>
				</tr>
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
					<th style="">Lateralidad:</th>
					<td><?php echo utf8_encode($qrDerivacion->Fields('LATERALIDAD')); ?></td>
				</tr>
				<tr>
					<th style="">Proceso:</th>
					<td><?php echo utf8_encode($qrDerivacion->Fields('PROCESO')); ?>.-</td>
				</tr>
				<tr>
					<th style="">Fecha Derivación:</th>
					<td><?php echo date("d-m-Y",strtotime($qrDerivacion->Fields('FECHA_DERIVACION'))); ?></td>
				</tr>
				<tr>
					<th>Categoria:</th>
					<td><?php echo utf8_encode($qrDerivacion->Fields('CATEGORIA')); ?></td>
				</tr>
				<tr>
					<th>Int. Sanitaria:</th>
					<td><?php echo utf8_encode($qrDerivacion->Fields('INTERVENCION_SANITARIA')); ?></td>
				</tr>
				<tr>
					<th>Patología:</th>
					<td><?php echo utf8_encode($qrDerivacion->Fields('PROBLEMA_SALUD')); ?></td>
				</tr>
			
			</table>
		</div>
</div>
<script>
	
</script>