<?php 
require_once '../../../../Connections/oirs.php';
require_once '../../../../includes/functions.inc.php';
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../../index.php');
exit; }

$usuario = $_SESSION['dni'];
$rutPaciente = $_REQUEST['rutPaciente'];

date_default_timezone_set('America/Santiago');
$hoy= date('Y-m-d');

$query_qrBuscaAlarmas = "SELECT * FROM $MM_oirs_DATABASE.2_alarmas WHERE ESTADO = 'activa' AND FECHA_ALARMA <= '$hoy' AND USUARIO_RECEPTOR = '$usuario' order by FECHA_ALARMA desc";
$qrBuscaAlarmas = $oirs->SelectLimit($query_qrBuscaAlarmas) or die($oirs->ErrorMsg());
$totalRows_qrBuscaAlarmas = $qrBuscaAlarmas->RecordCount();


?>

<table id="tblAlarmas" class="table table-hover table-sm">
	<thead class="table-info">
		<tr align="center">
			<th><font>Emisor</font></th>
			<th><font>Folio</font></th>
			<th><font>Mensaje</font></th>
			<th><font>Opciones</font></th>
		</tr>
	</thead>
	<tbody>	
		<?php while (!$qrBuscaAlarmas->EOF) { 

		$idAlarma = $qrBuscaAlarmas->Fields('ID_ALARMA');
		$idBitacora = $qrBuscaAlarmas->Fields('ID_BITACORA');
		$cumplida = $qrBuscaAlarmas->Fields('CUMPLIDA');

		$query_qrBuscaBitacora = "SELECT * FROM $MM_oirs_DATABASE.2_bitacora WHERE ID_BITACORA = '$idBitacora'";
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
			<td>
				<a href="#" data-dismiss="modal"><span class="badge badge-warning" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')"><?php echo $qrBuscaBitacora->Fields('FOLIO'); ?><br>
			</span></a><font size="3"><br><?php echo date("d-m-Y",strtotime($qrBuscaAlarmas->Fields('FECHA_ALARMA'))); ?></font>
			</td>
			<td>
				<font size="3"><?php 
				echo $qrBuscaAlarmas->Fields('MENSAJE');?><br> 
				<?php if($qrBuscaBitacora->Fields('RUTA_DOCUMENTO')!='' and $qrBuscaBitacora->Fields('SESION') != null){ ?>
				<span class=""><a target="_blank" class="btn btn-xs btn-success" href="2.0/vistas/bitacora/adjuntaDoc/<?php echo $ruta; ?>" ><i class="far fa-file-pdf"></i></a></span>
				<?php } 
				if ($qrBuscaBitacora->Fields('RUTA_AUDIO') != null) {?>
						<a href="#" onclick="$('#dvFrmPlayAudios').load('2.O/vistas/bitacora/modals/frmPlayAudios.php?idBitacora='+<?php echo $idBitacora ?>)"><span class="badge badge-warning"><i class="fas fa-play"></i></span></a>
						<a href="#" onclick="document.getElementById('audioBitacora').pause()"><span class="badge badge-warning"><i class="fas fa-stop"></i></span></a>
				<?php }
			?></font>
			</td>
			<td>
				<button class="btn btn btn-danger btn-xs" onclick="preguntaSiNofnDesprogramarTarea('<?php echo $idAlarma ?>','<?php echo $usuario ?>','<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')"><i class="fas fa-bell-slash"></i> Detener &nbsp;</button>
				<?php if ($cumplida=='') { ?>
					<button class="btn btn btn-success btn-xs" onclick="fnCheckearCumplida('<?php echo $idAlarma ?>','<?php echo $usuario ?>','<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')"><i class="fas fa-check"></i> Realizada</button>
				<?php }else{ ?>
					<button class="btn btn btn-danger btn-xs" onclick="fnCheckearNoCumplida('<?php echo $idAlarma ?>','<?php echo $usuario ?>','<?php echo $qrBuscaBitacora->Fields('ID_DERIVACION') ?>')"><i class="fas fa-times"></i> Quitar marca</button>
				<?php } ?>
			</td>
		</tr>	
	<?php
		$n++;
	 	$qrBuscaAlarmas->MoveNext();
	}
	?>
	</tbody>
</table>

<script type="text/javascript">

	function fnfrmBitacora(idDerivacion){

	    $('#dvfrmBitacora').load('2.0/vistas/bitacora/modals/frmBitacora.php?idDerivacion=' + idDerivacion);
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
			url:'2.0/vistas/bitacora/modals/desprogramarTarea.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se desprogramo correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvFrmAlarmasInicio').load('2.0/vistas/alarmas/modals/frmAlarmasInicio.php'); $("#menuSuperior").load('2.0/notificaciones/navbar.php'); }, 1500);//retardo PARA EVITAR dropdown
	    	}
			}
		});
	}

	function fnCheckearCumplida(idAlarma,usuario,idDerivacion){
		
		cadena = 'idAlarma=' + idAlarma +
				'&usuario=' + usuario +
				'&idDerivacion=' + idDerivacion;

		$.ajax({
			type:"post",
			data:cadena,
			url:'2.0/vistas/alarmas/php/marcaCumplida.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se marco como cumplida correctamente',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvFrmAlarmasInicio').load('2.0/vistas/alarmas/modals/frmAlarmasInicio.php'); }, 1);//retardo PARA EVITAR dropdown
	    	}
			}
		});
	}

	function fnCheckearNoCumplida(idAlarma,usuario,idDerivacion){

		cadena = 'idAlarma=' + idAlarma +
				'&usuario=' + usuario +
				'&idDerivacion=' + idDerivacion;

		$.ajax({
			type:"post",
			data:cadena,
			url:'2.0/vistas/alarmas/php/marcaNoCumplida.php',
			success:function(r){
				if (r == 1) {
					Swal.fire({
					  position: 'top-end',
					  icon: 'success',
					  title: 'Se quito marca cumplida',
					  showConfirmButton: false,
					  timer: 1500
					})
					setTimeout(function (){ $('#dvFrmAlarmasInicio').load('2.0/vistas/alarmas/modals/frmAlarmasInicio.php'); }, 1);//retardo PARA EVITAR dropdown
	    	}
			}
		});
	}

    $(function () {
	    $('#tblAlarmas').DataTable({
	      "paging": true,
	      "lengthChange": false,
	      "searching": true,
	      "ordering": true,
	      "info": true,
	      "autoWidth": false,
	      "responsive": true,
	      "order": [[ 1, 'asc' ]],
	      dom: 'lfrtip',
		    buttons: [ 'copy', 'excel', 'csv' ],
		    "language": {
		        "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
		        }
	    });
	  });
</script>