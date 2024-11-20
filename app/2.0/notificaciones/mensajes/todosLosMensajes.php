<?php 
require_once '../../../Connections/oirs.php';
require_once '../../../includes/functions.inc.php';
// Solo se permite el ingreso con el inicio de sesion.
session_start();
// Si el usuario no se ha logueado se le regresa al inicio.
if (!isset($_SESSION['loggedin'])) {
header('Location: ../../../index.php');
exit; } 

$usuario = $_REQUEST['usuario'];

$query_qrNotificación = "

SELECT 

2_notificaciones.ID_DERIVACION,
2_notificaciones.FOLIO,
2_notificaciones.FECHA,
2_notificaciones.ASUNTO,
2_notificaciones.MENSAJE,
pacientes.NOMBRE,
pacientes.COD_RUTPAC 

FROM $MM_oirs_DATABASE.2_notificaciones 

LEFT JOIN 2_derivaciones
ON 2_notificaciones.ID_DERIVACION = 2_derivaciones.ID_DERIVACION

LEFT JOIN pacientes
ON 2_derivaciones.COD_RUTPAC= pacientes.COD_RUTPAC

where 2_notificaciones.USUARIO='$usuario' order by 2_notificaciones.ID DESC

";
$qrNotificación = $oirs->SelectLimit($query_qrNotificación) or die($oirs->ErrorMsg());
$totalRows_qrNotificación = $qrNotificación->RecordCount();

?>
<div class="table-responsive-sm">
<table id="tTodosLosMensajes" class="table">
	<thead class="table-info">
		<tr align="center">
			<th><font size="2">Fecha de la Notificacion</font></th>
			<th><font size="2">Folio</font></th>
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
			<td><a href="#" data-dismiss="modal"><span class="badge badge-warning" data-toggle="modal" data-target="#modalBitacora" onclick="fnfrmBitacora('<?php echo $qrNotificación->Fields('ID_DERIVACION') ?>')"><?php echo $qrNotificación->Fields('FOLIO'); ?></span></a></td>
			<td><span><font size="2"><?php echo $qrNotificación->Fields('COD_RUTPAC'); ?></font></span></td>
			<td><span><font size="2"><?php echo utf8_encode($qrNotificación->Fields('NOMBRE')); ?></font></span></td>
			<td><span><font size="2"><?php echo utf8_encode($qrNotificación->Fields('ASUNTO')); ?></font></span></td>
			<td><span><font size="3"><?php echo utf8_encode($qrNotificación->Fields('MENSAJE')); ?></font></span></td>
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
		    $('#tTodosLosMensajes').DataTable({
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