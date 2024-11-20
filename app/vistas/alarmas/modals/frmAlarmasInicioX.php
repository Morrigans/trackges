<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$rutPaciente = $_REQUEST['rutPaciente'];

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$query_qrBuscaAlarmas = "SELECT * FROM $MM_oirs_DATABASE.alarmas WHERE ESTADO = 'activa' AND FECHA_ALARMA <= '$hoy' AND USUARIO_RECEPTOR = '$usuario' order by FECHA_ALARMA desc";
$qrBuscaAlarmas = $oirs->SelectLimit($query_qrBuscaAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrBuscaAlarmas = $qrBuscaAlarmas->RecordCount();


?>

<table id="tAlarmas" class="table table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font>Emisor</font></th>
			<th><font>Derivación</font></th>
			<th><font>Mensaje</font></th>
			<th><font>Opciones</font></th>
		</tr>
	</thead>
	<tbody>	
<?php while (!$qrBuscaAlarmas->EOF) { 

	$idAlarma = $qrBuscaAlarmas->Fields('ID_ALARMA');
	$idBitacora = $qrBuscaAlarmas->Fields('ID_BITACORA');

	$query_qrBuscaBitacora = "SELECT * FROM $MM_oirs_DATABASE.bitacora WHERE ID_BITACORA = '$idBitacora'";
	$qrBuscaBitacora = $oirs->SelectLimit($query_qrBuscaBitacora) or die($oirs->ErrorMsg());
	$totalRows_qrBuscaBitacora = $qrBuscaBitacora->RecordCount();

	$rutProfesional = $qrBuscaAlarmas->Fields('USUARIO_EMISOR');

    $query_slProfesional = ("SELECT * FROM $MM_oirs_DATABASE.login where USUARIO = '$rutProfesional'");
    $slProfesional = $oirs->SelectLimit($query_slProfesional) or die($oirs->ErrorMsg());
    $totalRows_slProfesional = $slProfesional->RecordCount();



	$ruta = $qrBuscaBitacora->Fields('RUTA_DOCUMENTO');
	?>
	<tr>
		<td><?php echo $slProfesional->Fields('NOMBRE'); ?></td>
		<td><a href="#" data-dismiss="modal"><span class="badge badge-warning" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')"><?php echo 'D0'.$qrBuscaBitacora->Fields('ID_DERIVACION'); ?><br>
		</span></a><font size="3"><br><?php echo date("d-m-Y",strtotime($qrBuscaAlarmas->Fields('FECHA_ALARMA'))); ?></font></td>
		<td><font size="3"><?php 
			echo utf8_encode($qrBuscaAlarmas->Fields('MENSAJE'));?><br> 
			<?php if($qrBuscaBitacora->Fields('RUTA_DOCUMENTO')!='' and $qrBuscaBitacora->Fields('SESION') != null){ ?>
			<span class=""><a target="_blank" class="btn btn-xs btn-success" href="vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
			<?php } 
			if ($qrBuscaBitacora->Fields('RUTA_AUDIO') != null) {?>
					<a href="#" onclick="$('#dvFrmPlayAudios').load('vistas/bitacora/modals/frmPlayAudios.php?idBitacora='+<?php echo $idBitacora ?>)"><span class="badge badge-warning"><i class="fas fa-play"></i></span></a>
					<a href="#" onclick="document.getElementById('audioBitacora').pause()"><span class="badge badge-warning"><i class="fas fa-stop"></i></span></a>
			<?php }
		?></font></td>
		<td><button class="btn btn btn-danger btn-xs" onclick="preguntaSiNofnDesprogramarTarea('<?php echo $idAlarma ?>','<?php echo $usuario ?>','<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')">Detener</button>

	</tr>
<?php
		$n++;
	 	$qrBuscaAlarmas->MoveNext();
	}
	?>

<script type="text/javascript">
	function fnfrmBitacora(idDerivacion){

	    $('#dvfrmBitacora').load('vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
	    $('body').addClass('modal-open');
	    //$('#modalAlarmasInicio').show();
	}

	function preguntaSiNofnDesprogramarTarea(idAlarma,usuario,idDerivacion){
		Swal.fire({
		  title: 'Estas Segur@?',
		  text: "Perderas la notificación de este evento!",
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, Quitar!'
		}).then((result) => {
		  if (result.isConfirmed) {
		  	fnDesprogramarTarea(idAlarma,usuario,idDerivacion)
		  }
		})
	}

	function fnDesprogramarTarea(idAlarma,usuario,idDerivacion){
		cadena = 'idAlarma=' + idAlarma;

		$.ajax({
			type:"post",
			data:cadena,
			url:'vistas/bitacora/modals/desprogramarTarea.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se desprogramo correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvFrmAlarmasInicio').load('vistas/alarmas/modals/frmAlarmasInicio.php'); }, 1501);//retardo PARA EVITAR dropdown
	    	}
			}
		});
	}
</script>