<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';

$rutPaciente = $_REQUEST['rutPaciente'];

$query_qrPac = "SELECT * FROM $MM_oirs_DATABASE.pacientes WHERE COD_RUTPAC='$rutPaciente' ";
$qrPac = $oirs->SelectLimit($query_qrPac) or die($oirs->ErrorMsg());
$totalRows_qrPac = $qrPac->RecordCount();

$correo= $qrPac->Fields('MAIL');
$fono= $qrPac->Fields('FONO');
$nombre= $qrPac->Fields('NOMBRE');
$direccion= $qrPac->Fields('DIRECCION');

?>

	<div class="card card-info">
		  	<div class="card-body">
			  	<div class="col-lg-12">
			  	  <!-- small box -->
			  	  <div class="small-box bg-default">
			  	    <div class="inner">
			  	      <p><h6>Nombre: <?php echo $nombre ?></h6></p>
			  	      <p><h6>Rut: <?php echo $rutPaciente ?></h6></p>
			  	      <p><h6>Correo: <?php echo $correo ?></h6></p>
			  	      <p><h6>Dirección: <?php echo $direccion ?></h6></p>
			  	    </div>
			  	  </div>
			  	</div>
		   	</div>
	</div>
