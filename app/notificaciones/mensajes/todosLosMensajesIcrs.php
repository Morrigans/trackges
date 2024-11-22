<?php 
require_once '../../Connections/oirs.php';
require_once '../../includes/functions.inc.php';
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../index.php');
exit; } 

$usuario = $_REQUEST['usuario'];

$query_qrNotificación = "

SELECT 

notificaciones_pp.ID_DERIVACION,
notificaciones_pp.FECHA,
notificaciones_pp.ASUNTO,
notificaciones_pp.MENSAJE,
pacientes.NOMBRE,
pacientes.COD_RUTPAC 

FROM $MM_oirs_DATABASE.notificaciones_pp 

LEFT JOIN derivaciones_pp
ON notificaciones_pp.ID_DERIVACION = derivaciones_pp.ID_DERIVACION

LEFT JOIN pacientes
ON derivaciones_pp.ID_PACIENTE= pacientes.ID

where notificaciones_pp.USUARIO='$usuario' order by notificaciones_pp.ID DESC

";
$qrNotificación = $oirs->SelectLimit($query_qrNotificación) or die($oirs->ErrorMsg());
$totalRows_qrNotificación = $qrNotificación->RecordCount();

?>
<div class="table-responsive-sm">
<table id="tTodosLosMensajesIcrs" class="table">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Fecha de la Notificacion</font></th>
			<th><font size="2">Derivación</font></th>
			<th><font size="2">Rut</font></th>
			<th><font size="2">Nombre paciente</font></th>
			<th><font size="2">Asunto</font></th>
			<th><font size="2">Notificación</font></th>
			<th><font size="2">Orden</font></th>
		</tr>
	</thead>
	<tbody>
	<?php
		$n=1;
	while (!$qrNotificación->EOF) {
	?>
		<tr>
			<td><font size="2"><?php echo date("d-m-Y",strtotime($qrNotificación->Fields('FECHA'))); ?></font></td>
			<td><a href="#" data-dismiss="modal"><span class="badge badge-warning" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrNotificación->Fields('ID_DERIVACION') ?>')"><?php echo 'P0'.$qrNotificación->Fields('ID_DERIVACION'); ?></span></a></td>
			<td><span><font size="2"><?php echo $qrNotificación->Fields('COD_RUTPAC'); ?></font></span></td>
			<td><span><font size="2"><?php echo $qrNotificación->Fields('NOMBRE'); ?></font></span></td>
			<td><span><font size="2"><?php echo $qrNotificación->Fields('ASUNTO'); ?></font></span></td>
			<td><span><font size="3"><?php echo $qrNotificación->Fields('MENSAJE'); ?></font></span></td>
			<td><span><font size="1"><?php echo $n; ?></font></span></td>
		</tr>
		<?php
		$n++;
	 	$qrNotificación->MoveNext();
	}
	?>
	</tbody>
</table>
</div>

<div class="modal-footer">	
 	<div align="right">
 	    <button type="button" class="btn btn-success" data-dismiss="modal">Cerrar Notificación</button>
	</div>	
</div>

<script>
		$(function () {
		    $('#tTodosLosMensajesIcrs').DataTable({
		      "paging": true,
		      "lengthChange": true,
		      "searching": true,
		      "ordering": true,
		      "info": true,
		      "autoWidth": true,
		      "responsive": true,
		      "order": [[ 6, 'desc' ]], 
		      dom: 'lBfrtip',
			    buttons: [
			                {
			                    extend: 'excelHtml5',
			                    exportOptions: {
			                        columns: [ 0, 1, 2, 3, 4, 5 ]
			                    }
			                },
			                {
			                    extend: 'pdfHtml5',
			                    exportOptions: {
			                        columns: [ 0, 1, 2, 3, 4, 5 ]
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